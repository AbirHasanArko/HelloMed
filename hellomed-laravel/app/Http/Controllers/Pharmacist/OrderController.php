<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\MedicineOrderItem;
use App\Models\MedicineOrder;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
    {
        $query = MedicineOrder::query()->with(['user', 'items.medicine']);

        $result = MedicineOrder::handleSearchAndFilters($request, $query, function ($order) {
            $customerName = $order->user ? $order->user->name : $order->customer_name;
            return [
                'id' => $order->id,
                'title' => 'Order #' . $order->id,
                'subtitle' => 'By: ' . $customerName . ' | Total: BDT ' . $order->total_amount
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('pharmacist.orders.index', [
            'orders' => $result->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function searchPatients(Request $request)
    {
        $query = $request->input('query');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = \App\Models\User::query()
            ->where('role', 'patient')
            ->where(function ($q) use ($query) {
                $q->where('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->take(10)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($patients);
    }

    public function searchMedicines(Request $request)
    {
        $query = $request->input('query');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $medicines = \App\Models\Medicine::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('generic_name', 'like', "%{$query}%")
            ->take(10)
            ->get(['id', 'name', 'generic_name', 'price', 'stock_quantity', 'requires_prescription']);

        return response()->json($medicines);
    }

    public function create()
    {
        return view('pharmacist.orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'customer_name' => ['required_without:user_id', 'nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_id' => ['required', 'exists:medicines,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cash,card,mobile_banking'],
        ]);

        $order = DB::transaction(function () use ($validated, $request) {
            $total = 0;
            $orderItems = [];
            
            $medicines = \App\Models\Medicine::query()
                ->whereIn('id', collect($validated['items'])->pluck('medicine_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($validated['items'] as $item) {
                $medicine = $medicines->get($item['medicine_id']);
                if (!$medicine || $medicine->stock_quantity < $item['quantity']) {
                    abort(422, "Insufficient stock for {$medicine->name}");
                }

                $lineTotal = $medicine->price * $item['quantity'];
                $total += $lineTotal;

                $orderItems[] = [
                    'medicine' => $medicine,
                    'quantity' => $item['quantity'],
                    'line_total' => $lineTotal,
                ];
            }

            $order = MedicineOrder::create([
                'user_id' => $validated['user_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'order_number' => 'POS-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'status' => 'completed',
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'delivery_address' => 'In-store Purchase',
                'phone' => $validated['phone'],
                'inventory_committed_at' => now(),
            ]);

            foreach ($orderItems as $item) {
                $item['medicine']->decrement('stock_quantity', $item['quantity']);
                $order->items()->create([
                    'medicine_id' => $item['medicine']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['medicine']->price,
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order;
        });

        AuditLogger::log('medicine_order.pos_created', $order, [], [
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
        ]);

        return redirect()->route('pharmacist.orders.index')->with('status', 'Offline sale completed successfully.');
    }

    public function update(Request $request, MedicineOrder $order)
    {
        $old = [
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ];

        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $lockedOrder = MedicineOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $lockedOrder->update($validated);

            $needsRelease = filled($lockedOrder->inventory_committed_at)
                && blank($lockedOrder->inventory_released_at)
                && ($lockedOrder->status === 'cancelled' || $lockedOrder->payment_status === 'refunded');

            if ($needsRelease) {
                MedicineOrderItem::query()
                    ->with('medicine')
                    ->where('medicine_order_id', $lockedOrder->id)
                    ->get()
                    ->each(function ($item): void {
                        if ($item->medicine) {
                            $item->medicine->increment('stock_quantity', $item->quantity);
                        }
                    });

                $lockedOrder->update([
                    'inventory_released_at' => now(),
                ]);
            }
        });

        $order->refresh();

        AuditLogger::log('medicine_order.updated', $order, $old, [
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'inventory_released_at' => optional($order->inventory_released_at)->toDateTimeString(),
        ]);

        return back()->with('status', 'Medicine order updated.');
    }

    public function prescription(MedicineOrder $order): StreamedResponse
    {
        abort_unless($order->prescription_path, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($order->prescription_path), 404);

        return $disk->response(
            $order->prescription_path,
            basename($order->prescription_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.basename($order->prescription_path).'"',
            ]
        );
    }

    public function invoice(MedicineOrder $order)
    {
        $order->load('items.medicine', 'user');

        $pdf = Pdf::loadView('pdfs.medicine-invoice', [
            'order' => $order,
        ]);

        return $pdf->download($order->order_number.'-invoice.pdf');
    }
}

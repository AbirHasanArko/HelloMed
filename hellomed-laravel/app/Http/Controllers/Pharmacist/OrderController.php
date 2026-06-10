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

class OrderController extends Controller
{
    public function index()
    {
        return view('pharmacist.orders.index', [
            'orders' => MedicineOrder::query()->with(['user', 'items.medicine'])->latest()->paginate(20),
        ]);
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
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicineCartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('medicine_cart', []);
        $medicineIds = array_keys($cart);
        $medicines = Medicine::query()->whereIn('id', $medicineIds)->get()->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $medicineId => $quantity) {
            $medicine = $medicines->get((int) $medicineId);
            if (! $medicine) {
                continue;
            }

            $lineTotal = (float) $medicine->price * (int) $quantity;
            $total += $lineTotal;

            $items[] = [
                'medicine' => $medicine,
                'quantity' => (int) $quantity,
                'line_total' => $lineTotal,
            ];
        }

        return view('shop.cart', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(Request $request, Medicine $medicine): RedirectResponse
    {
        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = $request->session()->get('medicine_cart', []);
        $existing = (int) ($cart[$medicine->id] ?? 0);
        $cart[$medicine->id] = min($medicine->stock_quantity, $existing + $qty);
        $request->session()->put('medicine_cart', $cart);

        return back()->with('status', 'Medicine added to cart.');
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        $qty = (int) $request->input('quantity', 1);
        $cart = $request->session()->get('medicine_cart', []);

        if ($qty <= 0) {
            unset($cart[$medicine->id]);
        } else {
            $cart[$medicine->id] = min($medicine->stock_quantity, $qty);
        }

        $request->session()->put('medicine_cart', $cart);

        return back()->with('status', 'Cart updated.');
    }

    public function remove(Request $request, Medicine $medicine): RedirectResponse
    {
        $cart = $request->session()->get('medicine_cart', []);
        unset($cart[$medicine->id]);
        $request->session()->put('medicine_cart', $cart);

        return back()->with('status', 'Medicine removed from cart.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_address' => ['required', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:30'],
            'payment_method' => ['required', 'in:cash-on-delivery,bkash,nagad'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'prescription' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $cart = $request->session()->get('medicine_cart', []);
        if ($cart === []) {
            return back()->withErrors(['cart' => 'Cart is empty.']);
        }

        $medicineIds = array_keys($cart);
        $medicines = Medicine::query()->whereIn('id', $medicineIds)->lockForUpdate()->get()->keyBy('id');

        $containsPrescriptionItems = false;
        foreach ($cart as $medicineId => $quantity) {
            $medicine = $medicines->get((int) $medicineId);
            if ($medicine && $medicine->requires_prescription) {
                $containsPrescriptionItems = true;
                break;
            }
        }

        if ($containsPrescriptionItems && ! $request->hasFile('prescription')) {
            return back()->withErrors([
                'prescription' => 'Prescription file is required for one or more medicines in your cart.',
            ])->withInput();
        }

        $prescriptionPath = null;
        if ($request->hasFile('prescription')) {
            $prescriptionPath = $request->file('prescription')->store('prescriptions', 'public');
        }

        $order = DB::transaction(function () use ($request, $validated, $cart, $medicines, $containsPrescriptionItems, $prescriptionPath) {
            $total = 0;
            $items = [];
            $commitInventoryNow = $validated['payment_method'] === 'cash-on-delivery';

            foreach ($cart as $medicineId => $quantity) {
                $medicine = $medicines->get((int) $medicineId);
                if (! $medicine) {
                    continue;
                }

                if ($medicine->stock_quantity < (int) $quantity) {
                    abort(422, "Insufficient stock for {$medicine->name}");
                }

                $lineTotal = (float) $medicine->price * (int) $quantity;
                $total += $lineTotal;

                $items[] = [
                    'medicine' => $medicine,
                    'quantity' => (int) $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $order = MedicineOrder::query()->create([
                'user_id' => $request->user()->id,
                'order_number' => 'MED-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'status' => 'pending',
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_callback_token' => in_array($validated['payment_method'], ['bkash', 'nagad'], true) ? Str::random(48) : null,
                'payment_status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'phone' => $validated['phone'],
                'notes' => $validated['notes'] ?? null,
                'prescription_path' => $prescriptionPath,
                'contains_prescription_items' => $containsPrescriptionItems,
                'inventory_committed_at' => $commitInventoryNow ? now() : null,
            ]);

            foreach ($items as $item) {
                if ($commitInventoryNow) {
                    $item['medicine']->decrement('stock_quantity', $item['quantity']);
                }
                $order->items()->create([
                    'medicine_id' => $item['medicine']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['medicine']->price,
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order;
        });

        AuditLogger::log('medicine_order.created', $order, [], [
            'payment_method' => $order->payment_method,
            'inventory_committed' => filled($order->inventory_committed_at),
        ]);

        $request->session()->forget('medicine_cart');

        if (in_array($order->payment_method, ['bkash', 'nagad'], true)) {
            return redirect()
                ->route('shop.payments.start', ['order' => $order, 'provider' => $order->payment_method])
                ->with('status', 'Order placed. Complete your payment to confirm the order.');
        }

        return redirect()->route('patient.medicine-orders.show', $order)->with('status', 'Medicine order placed successfully.');
    }
}

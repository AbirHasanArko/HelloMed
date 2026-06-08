<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MedicineOrder;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicinePaymentController extends Controller
{
    public function start(MedicineOrder $order, string $provider)
    {
        abort_unless($order->user_id === request()->user()->id, 403);
        abort_unless(in_array($provider, ['bkash', 'nagad'], true), 404);

        if (blank($order->payment_callback_token)) {
            $order->update([
                'payment_callback_token' => Str::random(48),
            ]);
        }

        return view('shop.payments.mock-gateway', [
            'order' => $order,
            'provider' => $provider,
        ]);
    }

    public function callback(Request $request, MedicineOrder $order, string $provider, string $status): RedirectResponse
    {
        abort_unless($order->user_id === request()->user()->id, 403);
        abort_unless(in_array($provider, ['bkash', 'nagad'], true), 404);
        abort_unless(in_array($status, ['success', 'failed'], true), 404);
        abort_unless($request->string('token')->toString() === (string) $order->payment_callback_token, 403);

        $old = [
            'payment_status' => $order->payment_status,
            'status' => $order->status,
        ];

        if ($status === 'success') {
            DB::transaction(function () use ($order): void {
                $lockedOrder = MedicineOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

                if (blank($lockedOrder->inventory_committed_at)) {
                    $items = $lockedOrder->items()->with('medicine')->get();
                    foreach ($items as $item) {
                        $medicine = $item->medicine;
                        if (! $medicine) {
                            continue;
                        }

                        if ($medicine->stock_quantity < $item->quantity) {
                            abort(422, "Insufficient stock for {$medicine->name} during payment confirmation.");
                        }

                        $medicine->decrement('stock_quantity', $item->quantity);
                    }
                }

                $lockedOrder->update([
                    'payment_status' => 'paid',
                    'status' => $lockedOrder->status === 'pending' ? 'processing' : $lockedOrder->status,
                    'inventory_committed_at' => $lockedOrder->inventory_committed_at ?? now(),
                    'payment_reference' => $lockedOrder->payment_reference ?? strtoupper($lockedOrder->payment_method).'-'.now()->format('YmdHis').'-'.$lockedOrder->id,
                ]);
            });

            $order->refresh();

            AuditLogger::log('medicine_order.payment_callback', $order, $old, [
                'payment_status' => $order->payment_status,
                'status' => $order->status,
            ], [
                'provider' => $provider,
                'callback_status' => 'success',
            ]);

            return redirect()->route('patient.medicine-orders.show', $order)->with('status', strtoupper($provider).' payment marked as paid.');
        }

        $order->update([
            'payment_status' => 'failed',
        ]);

        AuditLogger::log('medicine_order.payment_callback', $order, $old, [
            'payment_status' => $order->payment_status,
            'status' => $order->status,
        ], [
            'provider' => $provider,
            'callback_status' => 'failed',
        ]);

        return redirect()->route('patient.medicine-orders.show', $order)->with('status', strtoupper($provider).' payment marked as failed.');
    }
}

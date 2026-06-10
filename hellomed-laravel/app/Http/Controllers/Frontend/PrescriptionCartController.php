<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;

class PrescriptionCartController extends Controller
{
    public function addAll(Appointment $appointment): RedirectResponse
    {
        $this->authorize('view', $appointment);

        $items = $appointment->prescriptionItems()->with('medicine')->get();
        if ($items->isEmpty()) {
            return back()->withErrors(['prescription' => 'No prescribed medicines found for this appointment.']);
        }

        $cart = session('medicine_cart', []);
        $added = 0;

        foreach ($items as $item) {
            if (! $item->medicine) {
                continue;
            }

            $medicine = $item->medicine;
            if (! $medicine->is_active || $medicine->stock_quantity < 1) {
                continue;
            }

            $current = (int) ($cart[$medicine->id] ?? 0);
            $cart[$medicine->id] = min($medicine->stock_quantity, max(1, $current + 1));
            $added++;
        }

        if ($added === 0) {
            return back()->withErrors(['prescription' => 'No purchasable medicines are available from this prescription.']);
        }

        session()->put('medicine_cart', $cart);

        return redirect()->route('shop.cart')->with('status', 'All available prescribed medicines were added to cart.');
    }
}

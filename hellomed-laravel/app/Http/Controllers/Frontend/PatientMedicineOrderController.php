<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MedicineOrder;

class PatientMedicineOrderController extends Controller
{
    public function index()
    {
        return view('patient.medicine-orders', [
            'orders' => MedicineOrder::query()
                ->withCount('items')
                ->where('user_id', request()->user()->id)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(MedicineOrder $order)
    {
        abort_unless($order->user_id === request()->user()->id, 403);

        return view('patient.medicine-order-show', [
            'order' => $order->load('items.medicine'),
        ]);
    }
}

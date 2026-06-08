<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MedicineOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientMedicineInvoiceController extends Controller
{
    public function __invoke(MedicineOrder $order)
    {
        abort_unless($order->user_id === request()->user()->id, 403);

        $order->load('items.medicine', 'user');

        $pdf = Pdf::loadView('pdfs.medicine-invoice', [
            'order' => $order,
        ]);

        return $pdf->download($order->order_number.'-invoice.pdf');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminPaymentController extends Controller
{
    public function index()
    {
        return view('admin.payments.index', [
            'payments' => Payment::query()->with(['appointment.doctor', 'user'])->latest()->paginate(20),
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,failed,refunded'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update([
            ...$validated,
            'paid_at' => $validated['status'] === 'paid' ? now() : null,
        ]);

        $payment->appointment?->update([
            'payment_status' => $validated['status'] === 'paid' ? 'paid' : $validated['status'],
        ]);

        if ($payment->appointment) {
            Mail::raw(
                "Payment status for your appointment #{$payment->appointment_id} is now {$payment->status}.",
                function ($message) use ($payment): void {
                    $message->to($payment->appointment->patient_email)->subject('HelloMed Payment Status Updated');
                }
            );
        }

        return back()->with('status', 'Payment status updated.');
    }
}

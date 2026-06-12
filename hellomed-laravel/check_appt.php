<?php
$appts = \App\Models\Appointment::with(['doctor.user', 'user'])->get();
foreach ($appts as $appt) {
    if ($appt->user && $appt->user->name == 'Jarif Hossain') {
        echo "ID: " . $appt->id . "\n";
        echo "Status: " . $appt->status . "\n";
        echo "Payment Status: " . $appt->payment_status . "\n";
        echo "Doctor Cut: " . $appt->doctor_cut . "\n";
    }
}

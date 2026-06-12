<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\LabTestRequest;
use App\Models\MedicineOrder;
use App\Models\HospitalFundTransaction;
use App\Models\EmployeePayout;
use App\Models\User;

class SyncFinancials extends Command
{
    protected $signature = 'app:sync-financials {--month=}';
    protected $description = 'Synchronize financial records: hospital fund and employee payouts';

    public function handle()
    {
        $this->info('Starting financial synchronization...');

        $this->syncAppointments();
        $this->syncLabTests();
        $this->syncMedicineOrders();
        
        $month = $this->option('month') ?: date('Y-m');
        $this->generatePayouts($month);

        $this->info('Financials synced successfully!');
    }

    private function syncAppointments()
    {
        $appointments = Appointment::where('status', 'completed')
            ->whereNull('doctor_cut')
            ->get();

        foreach ($appointments as $appt) {
            $paid = (float) $appt->payments()->where('status', 'paid')->sum('amount');
            
            if ($paid == 0 && $appt->payment_status === 'paid') {
                $paid = $appt->service_mode === 'online'
                    ? (float) ($appt->doctor->online_fee ?? $appt->doctor->consultation_fee ?? 0)
                    : (float) ($appt->doctor->offline_fee ?? $appt->doctor->consultation_fee ?? 0);
            }

            if ($paid > 0) {
                $doctorRate = $appt->service_mode === 'online' ? 0.95 : 0.85;
                $appt->doctor_cut = $paid * $doctorRate;
                $appt->hospital_cut = $paid - $appt->doctor_cut;
                $appt->save();

                HospitalFundTransaction::firstOrCreate(
                    ['type' => 'appointment_cut', 'reference_id' => $appt->id],
                    ['amount' => $appt->hospital_cut]
                );
            } else if (in_array($appt->payment_status, ['not_required', 'pending'])) {
                // If it's literally 0, just mark it as 0 to prevent re-checking forever if it was free
                if ($appt->payment_status === 'not_required') {
                    $appt->doctor_cut = 0;
                    $appt->hospital_cut = 0;
                    $appt->save();
                }
            }
        }
    }

    private function syncLabTests()
    {
        $tests = LabTestRequest::where('payment_status', 'paid')->get();
        foreach ($tests as $test) {
            $fee = $test->availableTest->fee_bdt ?? 0;
            if ($fee > 0) {
                HospitalFundTransaction::firstOrCreate(
                    ['type' => 'test_fee', 'reference_id' => $test->id],
                    ['amount' => $fee]
                );
            }
        }
    }

    private function syncMedicineOrders()
    {
        $orders = MedicineOrder::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->with('items.medicine')
            ->get();

        foreach ($orders as $order) {
            $expense = 0;
            $sale = 0;
            foreach ($order->items as $item) {
                $buyingPrice = $item->medicine->buying_price ?? 0;
                $expense += $buyingPrice * $item->quantity;
                $sale += $item->unit_price * $item->quantity;
            }
            
            HospitalFundTransaction::firstOrCreate(
                ['type' => 'medicine_sale', 'reference_id' => $order->id],
                ['amount' => $sale]
            );
            HospitalFundTransaction::firstOrCreate(
                ['type' => 'medicine_expense', 'reference_id' => $order->id],
                ['amount' => $expense]
            );
        }
    }

    private function generatePayouts($month)
    {
        // Doctors
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        foreach ($doctors as $doctor) {
            if (!$doctor->doctorProfile) continue;

            $totalCut = Appointment::where('doctor_id', $doctor->doctorProfile->id)
                ->where('status', 'completed')
                ->whereNotNull('doctor_cut')
                ->where('scheduled_for', 'like', $month . '%')
                ->sum('doctor_cut');

            $payout = EmployeePayout::firstOrNew([
                'user_id' => $doctor->id,
                'month' => $month
            ]);

            if ($totalCut > ($payout->amount ?? 0)) {
                $payout->amount = $totalCut;
                // If it was already paid/confirmed but they earned more, reopen it!
                if ($payout->exists && $payout->status !== 'pending') {
                    $payout->status = 'pending';
                    $payout->paid_at = null;
                    $payout->confirmed_at = null;
                } else {
                    $payout->status = $payout->status ?? 'pending';
                }
                $payout->save();
            }
        }

        // Staff & Pharmacists
        $staff = User::whereIn('role', ['staff', 'pharmacist'])->where('is_active', true)->get();
        foreach ($staff as $user) {
            $payout = EmployeePayout::firstOrNew([
                'user_id' => $user->id,
                'month' => $month
            ]);

            $newAmount = $user->monthly_payment ?? 0;
            if ($newAmount > ($payout->amount ?? 0)) {
                $payout->amount = $newAmount;
                if ($payout->exists && $payout->status !== 'pending') {
                    $payout->status = 'pending';
                    $payout->paid_at = null;
                    $payout->confirmed_at = null;
                } else {
                    $payout->status = $payout->status ?? 'pending';
                }
                $payout->save();
            }
        }
    }
}

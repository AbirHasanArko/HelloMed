<?php

namespace App\Http\Controllers;

use App\Models\EmployeePayout;
use App\Models\HospitalFundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        
        $payouts = EmployeePayout::where('user_id', $user->id)->orderBy('month', 'desc')->get();
        $pendingConfirmations = $payouts->where('status', 'paid');
        
        $viewData = [
            'payouts' => $payouts,
            'pendingConfirmations' => $pendingConfirmations,
            'isAdmin' => $isAdmin,
        ];
        
        if ($isAdmin) {
            // Overall
            $totalStaffPayouts = EmployeePayout::join('users', 'users.id', '=', 'employee_payouts.user_id')
                ->where('users.role', '!=', 'doctor')
                ->whereIn('employee_payouts.status', ['paid', 'confirmed'])
                ->sum('employee_payouts.amount');

            $viewData['totalFund'] = HospitalFundTransaction::whereIn('type', ['appointment_cut', 'test_fee', 'medicine_sale'])->sum('amount') 
                                   - HospitalFundTransaction::where('type', 'medicine_expense')->sum('amount')
                                   - $totalStaffPayouts;
            $viewData['totalPayouts'] = EmployeePayout::whereIn('status', ['paid', 'confirmed'])->sum('amount');
            
            // Pharmacy
            $viewData['pharmacySales'] = HospitalFundTransaction::where('type', 'medicine_sale')->sum('amount');
            $viewData['pharmacyExpense'] = HospitalFundTransaction::where('type', 'medicine_expense')->sum('amount');
            
            $viewData['pharmacyMonthly'] = HospitalFundTransaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, type, SUM(amount) as total')
                ->whereIn('type', ['medicine_sale', 'medicine_expense'])
                ->groupBy('month', 'type')
                ->orderBy('month')
                ->get();

            // Tests
            $viewData['testRevenue'] = HospitalFundTransaction::where('type', 'test_fee')->sum('amount');

            // Consultancy & Doctor Filter
            $viewData['doctors'] = \App\Models\Doctor::all();
            
            $consultancyQuery = \App\Models\Appointment::where('status', 'completed');
            if ($request->filled('doctor_id')) {
                $consultancyQuery->where('doctor_id', $request->input('doctor_id'));
                $viewData['selectedDoctorId'] = $request->input('doctor_id');
            } else {
                $viewData['selectedDoctorId'] = null;
            }

            // Using Appointment table to calculate cuts so it responds perfectly to the doctor_id filter
            $viewData['doctorEarnings'] = (clone $consultancyQuery)->sum('doctor_cut');
            $viewData['hospitalConsultancyRevenue'] = (clone $consultancyQuery)->sum('hospital_cut');
            $viewData['totalAppointments'] = (clone $consultancyQuery)->count();

            // Monthly grouping for charts
            $allTransactions = HospitalFundTransaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, type, SUM(amount) as total')
                ->groupBy('month', 'type')
                ->orderBy('month')
                ->get();
                
            $monthlyIncomeExpenseMap = [];
            foreach ($allTransactions as $t) {
                $m = $t->month;
                if (!isset($monthlyIncomeExpenseMap[$m])) {
                    $monthlyIncomeExpenseMap[$m] = ['month' => $m, 'income' => 0, 'expense' => 0];
                }
                
                if ($t->type === 'medicine_expense') {
                    $monthlyIncomeExpenseMap[$m]['expense'] += (float) $t->total;
                } else {
                    $monthlyIncomeExpenseMap[$m]['income'] += (float) $t->total;
                }
            }

            $monthlyPayouts = EmployeePayout::selectRaw('month, SUM(amount) as total')
                ->whereIn('status', ['paid', 'confirmed'])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $viewData['monthlyPayouts'] = $monthlyPayouts;

            // Add Payouts to Expense
            foreach ($monthlyPayouts as $p) {
                $m = $p->month;
                if (!isset($monthlyIncomeExpenseMap[$m])) {
                    $monthlyIncomeExpenseMap[$m] = ['month' => $m, 'income' => 0, 'expense' => 0];
                }
                $monthlyIncomeExpenseMap[$m]['expense'] += (float) $p->total;
            }

            // Add Doctor Cuts to Income (since they are now counted as an expense when paid out)
            $monthlyDoctorCuts = \App\Models\Appointment::selectRaw('DATE_FORMAT(scheduled_for, "%Y-%m") as month, SUM(doctor_cut) as total')
                ->where('status', 'completed')
                ->groupBy('month')
                ->get();
            
            foreach ($monthlyDoctorCuts as $dc) {
                $m = $dc->month;
                if (!isset($monthlyIncomeExpenseMap[$m])) {
                    $monthlyIncomeExpenseMap[$m] = ['month' => $m, 'income' => 0, 'expense' => 0];
                }
                $monthlyIncomeExpenseMap[$m]['income'] += (float) $dc->total;
            }

            ksort($monthlyIncomeExpenseMap);
            $viewData['monthlyIncomeExpense'] = array_values($monthlyIncomeExpenseMap);

            // Payouts by Role
            $viewData['payoutsByRole'] = EmployeePayout::join('users', 'employee_payouts.user_id', '=', 'users.id')
                ->whereIn('employee_payouts.status', ['paid', 'confirmed'])
                ->selectRaw('users.role, SUM(employee_payouts.amount) as total')
                ->groupBy('users.role')
                ->get();
        } else {
            $viewData['myPayoutsChart'] = EmployeePayout::where('user_id', $user->id)
                ->orderBy('month')
                ->get();
        }

        return view('analytics.index', $viewData);
    }
    
    public function export(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin() && $request->input('type') === 'hospital_fund') {
            $data = HospitalFundTransaction::latest()->get();
            $filename = "hospital_fund_export.csv";
            $callback = function() use($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Type', 'Reference ID', 'Amount', 'Date']);
                foreach ($data as $row) {
                    fputcsv($file, [$row->id, $row->type, $row->reference_id, $row->amount, $row->created_at]);
                }
                fclose($file);
            };
        } else {
            $data = EmployeePayout::where('user_id', $user->id)->orderBy('month', 'desc')->get();
            $filename = "my_payouts.csv";
            $callback = function() use($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Month', 'Amount', 'Status', 'Paid At', 'Confirmed At']);
                foreach ($data as $row) {
                    fputcsv($file, [$row->month, $row->amount, $row->status, $row->paid_at, $row->confirmed_at]);
                }
                fclose($file);
            };
        }
        
        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
    
    public function confirmPayout(Request $request, EmployeePayout $payout)
    {
        if ($payout->user_id !== Auth::id()) {
            abort(403);
        }
        
        $payout->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
        
        return back()->with('success', 'Payment receipt confirmed successfully.');
    }
}

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
            $viewData['totalFund'] = HospitalFundTransaction::sum('amount');
            $viewData['totalPayouts'] = EmployeePayout::whereIn('status', ['paid', 'confirmed'])->sum('amount');
            
            // Monthly grouping for charts
            $viewData['monthlyFunds'] = HospitalFundTransaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                
            $viewData['monthlyPayouts'] = EmployeePayout::selectRaw('month, SUM(amount) as total')
                ->whereIn('status', ['paid', 'confirmed'])
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $viewData['fundByType'] = HospitalFundTransaction::selectRaw('type, SUM(amount) as total')
                ->groupBy('type')
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

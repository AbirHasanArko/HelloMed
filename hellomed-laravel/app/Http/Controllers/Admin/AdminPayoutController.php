<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeePayout;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminPayoutController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $payouts = EmployeePayout::with('user')
            ->where('month', $month)
            ->get();

        return view('admin.payouts.index', compact('payouts', 'month'));
    }

    public function generate(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        Artisan::call('app:sync-financials', ['--month' => $month]);
        
        return back()->with('success', "Payouts generated and financials synced for $month.");
    }

    public function markPaid(Request $request, EmployeePayout $payout)
    {
        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        if ($payout->user) {
            $payout->user->notify(new \App\Notifications\SystemNotification(
                'Payment Received',
                "Your payout of BDT " . number_format($payout->amount, 2) . " for {$payout->month} has been marked as paid. Please confirm receipt on your Analytics page.",
                'normal',
                route('analytics.index')
            ));
        }

        return back()->with('success', 'Payout marked as paid and user notified.');
    }
}

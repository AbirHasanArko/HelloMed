<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $expense = Medicine::query()->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(buying_price, 0) * stock_quantity'));
        $income = MedicineOrder::query()->where('payment_status', 'paid')->sum('total_amount');
        $profit = $income - $expense;

        return view('pharmacist.dashboard', [
            'medicineCount' => Medicine::query()->count(),
            'lowStockCount' => Medicine::query()->where('stock_quantity', '<=', 10)->count(),
            'pendingOrders' => MedicineOrder::query()->where('status', 'pending')->count(),
            'processingOrders' => MedicineOrder::query()->where('status', 'processing')->count(),
            'expense' => $expense,
            'income' => $income,
            'profit' => $profit,
        ]);
    }
}

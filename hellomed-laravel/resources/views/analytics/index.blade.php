@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<section class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1>Financial Analytics</h1>
        <div style="display: flex; gap: 10px;">
            @if($isAdmin)
                <a href="{{ route('analytics.export', ['type' => 'hospital_fund']) }}" class="button">Export Ledger CSV</a>
            @else
                <a href="{{ route('analytics.export', ['type' => 'my_payouts']) }}" class="button">Export My Payouts</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="color: var(--success-text); margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if($pendingConfirmations->count() > 0)
        <div class="card" style="border-left: 4px solid var(--warning-text); margin-bottom: 24px;">
            <h3 style="color: var(--warning-text); margin-top: 0;">Pending Payment Confirmations</h3>
            @foreach($pendingConfirmations as $pending)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                    <span>You received a payout of <strong>BDT {{ number_format($pending->amount, 2) }}</strong> for {{ $pending->month }}.</span>
                    <form action="{{ route('analytics.payouts.confirm', $pending) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="button button-primary">Confirm Receipt</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    @if($isAdmin)
        <!-- Global Stats -->
        <div class="grid cols-2" style="margin-bottom: 40px;">
            <div class="card" style="background: var(--primary); color: white;">
                <h3 style="margin-top: 0; color: rgba(255,255,255,0.8); font-size: 0.875rem; text-transform: uppercase;">Total Hospital Net Profit</h3>
                <p style="margin: 0; font-size: 2.2rem; font-weight: 600; color: white;">BDT {{ number_format($totalFund, 2) }}</p>
            </div>
            <div class="card">
                <h3 class="muted" style="margin-top: 0; font-size: 0.875rem; text-transform: uppercase;">Total Payouts Distributed</h3>
                <p style="margin: 0; font-size: 2.2rem; font-weight: 600; color: var(--error-text);">BDT {{ number_format($totalPayouts, 2) }}</p>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin-bottom: 40px;">

        <!-- Financial Overview Charts -->
        <div class="grid cols-2" style="grid-template-columns: 1fr 2fr; margin-bottom: 40px;">
            <div class="card">
                <h3 style="margin-top: 0;">Net Profit Breakdown</h3>
                <canvas id="profitPieChart" style="max-height: 280px;"></canvas>
            </div>
            <div class="card">
                <h3 style="margin-top: 0;">Income vs Expense (Monthly)</h3>
                <canvas id="incomeExpenseChart" style="max-height: 280px;"></canvas>
            </div>
        </div>

        <div class="card" style="margin-bottom: 40px;">
            <h3 style="margin-top: 0;">Payouts Distributed by Role</h3>
            <canvas id="payoutsRoleChart" style="max-height: 280px;"></canvas>
        </div>

        <!-- Section A: Pharmacy -->
        <div style="margin-bottom: 40px;">
            <h2>Pharmacy Performance</h2>
            <div class="grid cols-3" style="margin-bottom: 24px;">
                <div class="card" style="border-left: 4px solid #3b82f6;">
                    <div class="muted" style="font-size: 0.875rem; text-transform: uppercase;">Total Sales Revenue</div>
                    <div style="font-size: 1.5rem; font-weight: 600;">BDT {{ number_format($pharmacySales, 2) }}</div>
                </div>
                <div class="card" style="border-left: 4px solid var(--error-text);">
                    <div class="muted" style="font-size: 0.875rem; text-transform: uppercase;">Total Cost / Expense</div>
                    <div style="font-size: 1.5rem; font-weight: 600;">BDT {{ number_format($pharmacyExpense, 2) }}</div>
                </div>
                <div class="card" style="border-left: 4px solid var(--success-text);">
                    <div class="muted" style="font-size: 0.875rem; text-transform: uppercase;">Net Pharmacy Profit</div>
                    <div style="font-size: 1.5rem; font-weight: 600;">BDT {{ number_format($pharmacySales - $pharmacyExpense, 2) }}</div>
                </div>
            </div>
            
            <div class="card">
                <canvas id="pharmacyChart" style="max-height: 350px;"></canvas>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin-bottom: 40px;">

        <!-- Section B & C: Diagnostics and Consultancy -->
        <div class="grid cols-2" style="grid-template-columns: 1fr 2fr; margin-bottom: 40px;">
            <!-- Diagnostics -->
            <div>
                <h2>Diagnostics</h2>
                <div class="card" style="border-left: 4px solid #8b5cf6;">
                    <div class="muted" style="font-size: 0.875rem; text-transform: uppercase; margin-bottom: 8px;">Total Lab Test Revenue</div>
                    <div style="font-size: 2.5rem; font-weight: 600;">BDT {{ number_format($testRevenue, 2) }}</div>
                    <p class="muted" style="margin-top: 10px; font-size: 0.875rem;">Revenue from all completed lab tests and diagnostics.</p>
                </div>
            </div>

            <!-- Consultancy -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="margin: 0;">Consultancy</h2>
                    <form method="GET" action="{{ route('analytics.index') }}" style="display: flex; gap: 10px;">
                        <select name="doctor_id" class="form-input">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected($selectedDoctorId == $doctor->id)>Dr. {{ $doctor->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="button">Filter</button>
                    </form>
                </div>
                
                <div class="grid cols-2">
                    <div class="card" style="border-top: 4px solid #f59e0b;">
                        <div class="muted" style="font-size: 0.875rem; text-transform: uppercase;">Total Doctor Earnings</div>
                        <div style="font-size: 2rem; font-weight: 600; margin-top: 8px;">BDT {{ number_format($doctorEarnings, 2) }}</div>
                    </div>
                    <div class="card" style="border-top: 4px solid var(--success-text);">
                        <div class="muted" style="font-size: 0.875rem; text-transform: uppercase;">Hospital Consultancy Revenue</div>
                        <div style="font-size: 2rem; font-weight: 600; margin-top: 8px;">BDT {{ number_format($hospitalConsultancyRevenue, 2) }}</div>
                    </div>
                </div>
                <div class="muted" style="margin-top: 16px; font-size: 0.875rem;">
                    Showing statistics for <strong>{{ $totalAppointments }}</strong> completed appointment(s).
                </div>
            </div>
        </div>
        
        <script>
            // Pharmacy Chart
            const pCtx = document.getElementById('pharmacyChart').getContext('2d');
            const pMonthly = {!! json_encode($pharmacyMonthly) !!};
            
            const pLabels = [...new Set(pMonthly.map(i => i.month))].sort();
            const pSales = pLabels.map(l => {
                const item = pMonthly.find(i => i.month === l && i.type === 'medicine_sale');
                return item ? parseFloat(item.total) : 0;
            });
            const pExpense = pLabels.map(l => {
                const item = pMonthly.find(i => i.month === l && i.type === 'medicine_expense');
                return item ? parseFloat(item.total) : 0;
            });

            new Chart(pCtx, {
                type: 'bar',
                data: {
                    labels: pLabels,
                    datasets: [
                        { label: 'Sales', data: pSales, backgroundColor: '#3b82f6' },
                        { label: 'Expense', data: pExpense, backgroundColor: '#ef4444' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // --- New Charts Logic ---
            const pharmacyNet = {{ $pharmacySales - $pharmacyExpense }};
            const testRevenue = {{ $testRevenue }};
            const consultancyRevenue = {{ $hospitalConsultancyRevenue }};
            
            new Chart(document.getElementById('profitPieChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Pharmacy Profit', 'Diagnostics', 'Consultancy'],
                    datasets: [{
                        data: [pharmacyNet > 0 ? pharmacyNet : 0, testRevenue, consultancyRevenue],
                        backgroundColor: ['#10b981', '#8b5cf6', '#f59e0b']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            const monthlyIE = {!! json_encode($monthlyIncomeExpense) !!};
            new Chart(document.getElementById('incomeExpenseChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: monthlyIE.map(i => i.month),
                    datasets: [
                        { label: 'Income', data: monthlyIE.map(i => i.income), backgroundColor: '#4ade80' },
                        { label: 'Expense', data: monthlyIE.map(i => i.expense), backgroundColor: '#ef4444' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            const payoutsRole = {!! json_encode($payoutsByRole) !!};
            new Chart(document.getElementById('payoutsRoleChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: payoutsRole.map(i => i.role.toUpperCase()),
                    datasets: [{
                        label: 'Total Distributed',
                        data: payoutsRole.map(i => i.total),
                        backgroundColor: '#3b82f6'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        </script>
    @else
        <!-- Employee View -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-top: 0;">My Income History</h3>
            <canvas id="myIncomeChart" style="max-height: 400px;"></canvas>
        </div>

        <script>
            const ctx = document.getElementById('myIncomeChart').getContext('2d');
            const myPayouts = {!! json_encode($myPayoutsChart) !!};
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: myPayouts.map(i => i.month),
                    datasets: [{
                        label: 'Monthly Payout',
                        data: myPayouts.map(i => parseFloat(i.amount)),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        </script>
    @endif
</section>
@endsection

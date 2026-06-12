@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 2rem; color: #1e293b;">Financial Analytics</h1>
        <div style="display: flex; gap: 10px;">
            @if($isAdmin)
                <a href="{{ route('analytics.export', ['type' => 'hospital_fund']) }}" style="padding: 8px 16px; background: #0f172a; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">Export Hospital Fund CSV</a>
            @else
                <a href="{{ route('analytics.export', ['type' => 'my_payouts']) }}" style="padding: 8px 16px; background: #0f172a; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">Export My Payouts CSV</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if($pendingConfirmations->count() > 0)
        <div style="background: #fef08a; border-radius: 12px; padding: 20px; margin-bottom: 24px; display: flex; flex-direction: column; gap: 10px;">
            <h3 style="margin: 0; color: #854d0e;">Pending Payment Confirmations</h3>
            @foreach($pendingConfirmations as $pending)
                <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 12px; border-radius: 6px;">
                    <span>You received a payout of <strong>BDT {{ number_format($pending->amount, 2) }}</strong> for {{ $pending->month }}.</span>
                    <form action="{{ route('analytics.payouts.confirm', $pending) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button style="padding: 6px 12px; background: #16a34a; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirm Receipt</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    @if($isAdmin)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #64748b; font-size: 0.875rem; text-transform: uppercase;">Total Hospital Fund</h3>
                <p style="margin: 0; font-size: 2rem; font-weight: 600; color: #16a34a;">BDT {{ number_format($totalFund, 2) }}</p>
            </div>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #64748b; font-size: 0.875rem; text-transform: uppercase;">Total Paid to Employees</h3>
                <p style="margin: 0; font-size: 2rem; font-weight: 600; color: #dc2626;">BDT {{ number_format($totalPayouts, 2) }}</p>
            </div>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #64748b; font-size: 0.875rem; text-transform: uppercase;">Net Profit</h3>
                @php $net = $totalFund - $totalPayouts; @endphp
                <p style="margin: 0; font-size: 2rem; font-weight: 600; color: {{ $net >= 0 ? '#16a34a' : '#dc2626' }};">BDT {{ number_format($net, 2) }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #1e293b;">Revenue vs Payouts</h3>
                <canvas id="revenueChart"></canvas>
            </div>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #1e293b;">Income by Category</h3>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        
        <script>
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const monthlyFunds = {!! json_encode($monthlyFunds) !!};
            const monthlyPayouts = {!! json_encode($monthlyPayouts) !!};
            
            // Merge labels
            const labelsSet = new Set();
            monthlyFunds.forEach(i => labelsSet.add(i.month));
            monthlyPayouts.forEach(i => labelsSet.add(i.month));
            const labels = Array.from(labelsSet).sort();

            const fundData = labels.map(l => {
                const item = monthlyFunds.find(i => i.month === l);
                return item ? parseFloat(item.total) : 0;
            });

            const payoutData = labels.map(l => {
                const item = monthlyPayouts.find(i => i.month === l);
                return item ? parseFloat(item.total) : 0;
            });

            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Hospital Fund Revenue', data: fundData, backgroundColor: '#16a34a' },
                        { label: 'Employee Payouts', data: payoutData, backgroundColor: '#dc2626' }
                    ]
                },
                options: { responsive: true }
            });

            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const fundByType = {!! json_encode($fundByType) !!};
            
            new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: fundByType.map(i => i.type.replace('_', ' ').toUpperCase()),
                    datasets: [{
                        data: fundByType.map(i => parseFloat(i.total)),
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#f59e0b']
                    }]
                },
                options: { responsive: true }
            });
        </script>
    @else
        <!-- Employee View -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 24px;">
            <h3 style="margin-top: 0; color: #1e293b;">My Income History</h3>
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
</div>
@endsection

@extends('layouts.app')

@section('content')
    <section class="section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1>Employee Payouts Management</h1>
            <form action="{{ route('admin.payouts.index') }}" method="GET" style="display: flex; gap: 10px;">
                <input type="month" name="month" value="{{ $month }}" class="form-input">
                <button type="submit" class="button">Filter</button>
            </form>
        </div>

        @if(session('success'))
            <div style="color: var(--success-text); margin-bottom: 16px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <form action="{{ route('admin.payouts.generate') }}" method="POST" style="margin-bottom: 20px;">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" class="button button-primary">
                    Generate / Sync Payouts for {{ date('F Y', strtotime($month . '-01')) }}
                </button>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                        <tr>
                            <td><strong>{{ $payout->user->name }}</strong></td>
                            <td style="text-transform: capitalize;">{{ $payout->user->role }}</td>
                            <td>BDT {{ number_format($payout->amount, 2) }}</td>
                            <td>
                                @if($payout->status === 'pending')
                                    <span style="color: var(--warning-text); font-weight: 500;">Pending</span>
                                @elseif($payout->status === 'paid')
                                    <span style="color: var(--primary); font-weight: 500;">Paid (Waiting Confirm)</span>
                                @else
                                    <span style="color: var(--success-text); font-weight: 500;">Confirmed</span>
                                @endif
                            </td>
                            <td>
                                @if($payout->status === 'pending')
                                    <form action="{{ route('admin.payouts.mark-paid', $payout) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="button">Mark as Paid</button>
                                    </form>
                                @else
                                    <span class="muted" style="font-size: 0.875rem;">Paid at {{ $payout->paid_at ? $payout->paid_at->format('M d, Y') : 'N/A' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted" style="text-align: center; padding: 24px;">No payouts found for this month. Click Generate.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

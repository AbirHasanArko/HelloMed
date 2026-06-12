@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 2rem; color: #1e293b;">Employee Payouts Management</h1>
        <form action="{{ route('admin.payouts.index') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="month" name="month" value="{{ $month }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
            <button type="submit" style="padding: 8px 16px; background: #0f172a; color: white; border: none; border-radius: 6px; cursor: pointer;">Filter</button>
        </form>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 24px;">
        <form action="{{ route('admin.payouts.generate') }}" method="POST" style="margin-bottom: 20px;">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                Generate / Sync Payouts for {{ $month }}
            </button>
        </form>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                    <th style="padding: 12px;">Employee</th>
                    <th style="padding: 12px;">Role</th>
                    <th style="padding: 12px;">Amount</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px; font-weight: 500;">{{ $payout->user->name }}</td>
                        <td style="padding: 12px; text-transform: capitalize;">{{ $payout->user->role }}</td>
                        <td style="padding: 12px;">BDT {{ number_format($payout->amount, 2) }}</td>
                        <td style="padding: 12px;">
                            @if($payout->status === 'pending')
                                <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 99px; font-size: 0.875rem;">Pending</span>
                            @elseif($payout->status === 'paid')
                                <span style="background: #bfdbfe; color: #1e40af; padding: 4px 8px; border-radius: 99px; font-size: 0.875rem;">Paid (Waiting Confirm)</span>
                            @else
                                <span style="background: #bbf7d0; color: #166534; padding: 4px 8px; border-radius: 99px; font-size: 0.875rem;">Confirmed</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            @if($payout->status === 'pending')
                                <form action="{{ route('admin.payouts.mark-paid', $payout) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.875rem;">Mark as Paid</button>
                                </form>
                            @else
                                <span style="color: #64748b; font-size: 0.875rem;">Paid at {{ $payout->paid_at->format('M d, Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #64748b;">No payouts found for this month. Click Generate.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

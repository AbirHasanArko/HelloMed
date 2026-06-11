@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Payments</h1>
        <p>Optional appointment payment records and verification workflow.</p>
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Appointment</th>
                        <th>Patient</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>#{{ $payment->appointment_id }} · {{ $payment->appointment?->doctor?->name }}</td>
                            <td>{{ $payment->user?->name ?? 'Guest' }}</td>
                            <td>{{ $payment->method }}</td>
                            <td>BDT {{ number_format((float) $payment->amount, 2) }}</td>
                            <td>{{ $payment->status }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status">
                                        @foreach (['pending', 'paid', 'failed', 'refunded'] as $status)
                                            <option value="{{ $status }}" @selected($payment->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="reference" placeholder="reference" value="{{ $payment->reference }}">
                                    <input type="text" name="notes" placeholder="notes" value="{{ $payment->notes }}">
                                    <button class="button" type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 20px;">{{ $payments->links() }}</div>
        </div>
    </section>
@endsection

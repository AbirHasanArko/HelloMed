@extends('layouts.app')

@section('content')
<section class="section">
    <h1>My health records</h1>
    <p>Unified timeline of your appointments, prescriptions, and medicine orders.</p>



    <div class="grid cols-2">
        <div class="card">
            <h3>Appointment history</h3>
            <div class="list">
                @forelse ($appointments as $appointment)
                    <div class="list-item">
                        <strong>{{ $appointment->doctor?->name }} · {{ ucfirst($appointment->status) }}</strong>
                        <p>{{ $appointment->scheduled_for?->format('M d, Y h:i A') }} · {{ $appointment->department?->name }}</p>
                        @if ($appointment->doctor_prescription)
                            <a class="ghost-button" href="{{ route('patient.appointments.show', $appointment) }}">View prescription</a>
                        @endif
                    </div>
                @empty
                    <div class="list-item">No appointment records yet.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h3>Medicine order history</h3>
            <div class="list">
                @forelse ($medicineOrders as $order)
                    <div class="list-item">
                        <strong>{{ $order->order_number }} · {{ ucfirst($order->status) }}</strong>
                        <p>Payment: {{ ucfirst($order->payment_status) }} · BDT {{ number_format((float) $order->total_amount, 2) }}</p>
                        <a class="ghost-button" href="{{ route('patient.medicine-orders.show', $order) }}">View order</a>
                    </div>
                @empty
                    <div class="list-item">No medicine order records yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 20mm 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.5;
            margin: 0;
            background-color: #ffffff;
        }

        .header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .brand-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .brand-row td {
            vertical-align: middle;
        }

        .logo {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: #0d9488; /* Teal primary */
            color: #ffffff;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            line-height: 56px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: bold;
            color: #0f766e;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #586e75;
            margin-top: 2px;
        }

        .prescription-tag {
            display: inline-block;
            font-size: 12px;
            color: #042f2e;
            border: 1px solid #5eead4;
            border-radius: 999px;
            padding: 5px 12px;
            background: #ccfbf1;
            font-weight: bold;
        }

        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        
        .meta-grid td {
            width: 33.33%;
            padding-right: 10px;
            vertical-align: top;
        }

        .meta-card {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #14b8a6;
            border-radius: 8px;
            background: #f8fafc;
            padding: 10px 12px;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .meta-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .meta-value {
            color: #0f172a;
            font-weight: bold;
            font-size: 13px;
        }

        .section {
            margin-top: 24px;
        }

        .section-title {
            font-size: 14px;
            color: #0f766e;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .box {
            font-size: 13px;
            color: #334155;
            white-space: pre-wrap;
            padding-left: 4px;
        }

        .medicine-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .medicine-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 2px solid #cbd5e1;
        }

        .medicine-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 12px;
        }

        .medicine-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .buy-link {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 16px;
            background-color: #0d9488;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .safety-note {
            background-color: #fff1f2;
            border-left: 4px solid #f43f5e;
            padding: 12px;
            border-radius: 0 8px 8px 0;
            color: #881337;
            font-size: 12px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: right;
        }

        .signature {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            border-bottom: 1px dashed #94a3b8;
            margin-bottom: 8px;
            height: 40px;
        }

        .doc-name {
            font-weight: bold;
            font-size: 14px;
            color: #0f172a;
        }

        .doc-qual {
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="brand-row">
            <tr>
                <td style="width:70px;"><div class="logo">HM</div></td>
                <td>
                    <div class="brand-title">HelloMed Hospital</div>
                    <div class="brand-subtitle">Integrated Care & Digital Prescription</div>
                </td>
                <td style="text-align:right; width:150px; vertical-align: top;">
                    <span class="prescription-tag">E-Prescription</span>
                </td>
            </tr>
        </table>

        <table class="meta-grid">
            <tr>
                <td>
                    <div class="meta-card">
                        <div class="meta-label">Patient Info</div>
                        <div class="meta-value" style="font-size: 14px;">{{ $appointment->patient_name }}</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ $appointment->patient_phone }}</div>
                    </div>
                </td>
                <td>
                    <div class="meta-card">
                        <div class="meta-label">Consultation Date</div>
                        <div class="meta-value">{{ $appointment->scheduled_for?->format('M d, Y h:i A') }}</div>
                    </div>
                </td>
                <td>
                    <div class="meta-card">
                        <div class="meta-label">Follow-up Date</div>
                        <div class="meta-value">{{ $appointment->prescription_follow_up_date?->format('M d, Y') ?: 'Not Required' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if ($appointment->prescription_diagnosis)
    <div class="section">
        <div class="section-title">Clinical Diagnosis</div>
        <div class="box">{{ $appointment->prescription_diagnosis }}</div>
    </div>
    @endif

    @if ($appointment->prescriptionItems->isNotEmpty())
        <div class="section">
            <div class="section-title">Structured Medicine Plan</div>
            <table class="medicine-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Intake Time</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointment->prescriptionItems as $item)
                        <tr>
                            <td style="font-weight: bold;">{{ $item->medicine_name }}</td>
                            <td>{{ $item->dosage ?: '-' }}</td>
                            <td>{{ $item->intake_time ?: '-' }}</td>
                            <td>{{ $item->amount ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 16px; font-size: 12px; color: #475569;">
                <strong>Order Medicines:</strong> You can purchase these prescribed medicines directly from our pharmacy.<br>
                <a href="{{ route('patient.appointments.buy-all-medicines', $appointment) }}" style="color: #0d9488; text-decoration: underline;">Order from HelloMed Pharmacy</a>
            </div>
        </div>
    @elseif ($appointment->prescription_medicines)
        <div class="section">
            <div class="section-title">Medicines & Dosage</div>
            <div class="box">{{ $appointment->prescription_medicines }}</div>
        </div>
    @endif

    @if ($appointment->prescription_advice)
    <div class="section">
        <div class="section-title">Clinical Advice & Instructions</div>
        <div class="box">{{ $appointment->prescription_advice }}</div>
    </div>
    @endif

    @if ($appointment->prescription_safety_notes)
        <div class="safety-note">
            <strong>Safety Warning:</strong><br>
            {{ $appointment->prescription_safety_notes }}
        </div>
    @endif

    <div class="footer">
        <div class="signature">
            <div class="signature-line"></div>
            <div class="doc-name">{{ $appointment->doctor?->name }}</div>
            <div class="doc-qual">{{ $appointment->doctor?->qualification ?: 'Consultant' }}</div>
            <div class="doc-qual">{{ $appointment->doctor?->department?->name }}</div>
        </div>
    </div>
</body>
</html>

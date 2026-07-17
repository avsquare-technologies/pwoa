@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Entry Pass - {{ $event->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 40px 0;
        }

        .ticket-wrapper {
            width: 450px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .ticket-header {
            background-color: #1a1a1a;
            color: #ffffff;
            text-align: center;
            padding: 24px;
        }

        .ticket-logo {
            height: 36px;
            margin-bottom: 8px;
        }

        .ticket-header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.8);
        }

        .ticket-header p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ticket-body {
            padding: 0;
            text-align: center;
        }

        .qr-section {
            padding: 30px 20px 20px 20px;
            background-color: #ffffff;
        }

        .qr-container {
            display: inline-block;
            padding: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 50px;
            margin-bottom: 8px;
        }

        .status-valid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-invalid {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .ticket-id {
            font-family: monospace;
            font-size: 12px;
            color: #64748b;
            margin: 5px 0 0 0;
        }

        /* Ticket Divider */
        .ticket-divider {
            position: relative;
            height: 1px;
            border-top: 2px dashed #cbd5e1;
            margin: 0 24px;
        }

        .details-section {
            padding: 30px;
            background-color: #ffffff;
            text-align: left;
        }

        .holder-section {
            text-align: center;
            margin-bottom: 24px;
        }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 4px;
            display: block;
        }

        .holder-name {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
        }

        .event-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #f1f5f9;
        }

        .event-title {
            font-size: 16px;
            font-weight: 700;
            color: #2563eb;
            margin-top: 0;
            margin-bottom: 16px;
            text-align: center;
            line-height: 1.4;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-val {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            display: block;
        }

        .location-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        .location-val {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.4;
        }

        .ticket-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        
        <!-- Header -->
        <div class="ticket-header">
            @if(file_exists(public_path('assets/pwoa-logo.png')))
                <img src="{{ public_path('assets/pwoa-logo.png') }}" class="ticket-logo" alt="PWOA Logo">
            @else
                <div style="font-size: 20px; font-weight: bold; margin-bottom: 8px;">PWOA</div>
            @endif
            <h2>Your Entry Pass</h2>
            <p>Show this at event entry</p>
        </div>

        <div class="ticket-body">
            <!-- QR Code -->
            <div class="qr-section">
                <div class="qr-container">
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(180)->margin(0)->generate(json_encode(['ticket_id' => $attendee->ticket_id, 'token' => $attendee->token]))) }}" style="width: 180px; height: 180px;">
                </div>
                <div>
                    <span class="status-badge {{ $attendee->status === 'valid' ? 'status-valid' : 'status-invalid' }}">
                        {{ $attendee->status === 'valid' ? 'Valid Ticket' : 'Used / Invalid' }}
                    </span>
                </div>
                <div class="ticket-id">
                    ID: {{ $attendee->ticket_id }}
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="ticket-divider"></div>

            <!-- Details -->
            <div class="details-section">
                
                <!-- Pass Holder -->
                <div class="holder-section">
                    <span class="section-label">Pass Holder</span>
                    <h3 class="holder-name">{{ strtoupper($attendee->user->name ?? '') }}</h3>
                </div>

                <!-- Event Details Box -->
                <div class="event-box">
                    <h4 class="event-title">{{ $event->title }}</h4>
                    
                    <table class="info-table">
                        <tr>
                            <td style="width: 50%;">
                                <span class="section-label">Date</span>
                                <span class="info-val">{{ $event->starts_at->format('M d, Y') }}</span>
                            </td>
                            <td style="width: 50%; text-align: right;">
                                <span class="section-label" style="text-align: right;">Time</span>
                                <span class="info-val">{{ $event->starts_at->format('h:i A') }}</span>
                            </td>
                        </tr>
                    </table>

                    <div class="location-section">
                        <span class="section-label">Location</span>
                        <span class="location-val">{{ $event->location ?? 'Location not available' }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="ticket-footer">
                    Powered by PWOA
                </div>

            </div>

        </div>

    </div>

</body>
</html>

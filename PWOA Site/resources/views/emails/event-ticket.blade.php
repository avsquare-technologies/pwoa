<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px;
        }
        .ticket-box {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .event-title {
            color: #007bff;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
        }
        .details-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .label {
            font-weight: bold;
            color: #666;
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>PWOA Event Registration</h2>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $attendee->user->name }}</strong>,</p>
            <p>Success! You are registered for our upcoming event. Below are your ticket details.</p>
            
            <div class="ticket-box">
                <h3 class="event-title">{{ $event->title }}</h3>
                <table class="details-table">
                    <tr>
                        <td class="label">Date</td>
                        <td>{{ $event->starts_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Time</td>
                        <td>{{ $event->starts_at->format('h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Location</td>
                        <td>{{ $event->location ?? 'TBA' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ticket ID</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $attendee->ticket_id }}</td>
                    </tr>
                </table>
                
                <a href="{{ route('events.ticket', [$event->slug, $attendee->ticket_id]) }}" class="btn">View Digital Ticket & QR Code</a>
            </div>
            
            <p>Please have your digital ticket ready on your phone when you arrive at the venue. Our staff will scan your QR code for entry.</p>
            
            <p>We look forward to seeing you there!</p>
            <p>Best regards,<br>The PWOA Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Pressure Washers of America. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>

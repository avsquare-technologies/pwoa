<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Message Received - PWOA</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa; padding:20px;">
<tr>
<td align="center">

    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">

        {{-- HEADER --}}
        <tr>
            <td style="background:#0095d7; padding:30px 20px; text-align:center; color:#ffffff;">
                <h2 style="margin:0; font-size: 24px; font-weight: bold; letter-spacing: 0.5px;">📋 Message Received</h2>
                <p style="margin:5px 0 0; font-size:14px; opacity:0.9;">Pressure Washers of America</p>
            </td>
        </tr>

        {{-- BODY --}}
        <tr>
            <td style="padding:30px 25px; color:#333333; line-height:1.6;">
                <h3 style="margin-top:0; color:#0f172a;">Hi {{ $data['name'] }},</h3>
                
                <p style="margin-bottom:20px; color:#475569;">
                    Thank you for reaching out to us! We have successfully received your inquiry through the PWOA website contact form.
                </p>

                <p style="margin-bottom:25px; color:#475569;">
                    Our team is currently reviewing your request and will get back to you as soon as possible (usually within 1–2 business days).
                </p>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;" />

                <h4 style="margin: 0 0 15px; color:#0f172a; font-size:16px;">Copy of Your Inquiry</h4>

                <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; font-size: 14px;">
                    <tr>
                        <td style="background:#f8fafc; font-weight: bold; width: 30%; color:#64748b;">Inquiry Type</td>
                        <td style="background:#f8fafc; color:#334155;">{{ $data['inquiry_type'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color:#64748b;">Subject</td>
                        <td style="color:#334155;">{{ $data['subject'] }}</td>
                    </tr>
                    @if(!empty($data['phone']))
                    <tr>
                        <td style="background:#f8fafc; font-weight: bold; color:#64748b;">Phone</td>
                        <td style="background:#f8fafc; color:#334155;">{{ $data['phone'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($data['company']))
                    <tr>
                        <td style="font-weight: bold; color:#64748b;">Company</td>
                        <td style="color:#334155;">{{ $data['company'] }}</td>
                    </tr>
                    @endif
                </table>

                <div style="margin-top:20px;">
                    <strong style="font-size:14px; color:#64748b;">Message:</strong>
                    <div style="margin-top:8px; padding:15px; background:#f8fafc; border-left:4px solid #0095d7; border-radius: 4px; font-size:14px; color:#334155; white-space: pre-wrap;">{{ $data['message'] }}</div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;" />

                <p style="margin-bottom:0; font-size:14px; color:#64748b; text-align:center;">
                    If you have any urgent follow-up details to add, feel free to reply directly to this email.
                </p>
            </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
            <td style="background:#0f172a; padding:25px; text-align:center; font-size:12px; color:#94a3b8;">
                <p style="margin:0 0 8px;"><strong>Pressure Washers of America (PWOA)</strong></p>
                <p style="margin:0; opacity:0.8;">Connecting and elevating pressure washing professionals nationwide.</p>
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>

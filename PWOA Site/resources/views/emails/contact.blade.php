<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa; padding:20px;">
<tr>
<td align="center">

    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden;">

        {{-- HEADER --}}
        <tr>
            <td style="background:#1f3c88; padding:20px; text-align:center; color:#ffffff;">
                <h2 style="margin:0;">📩 New Contact Message</h2>
                <p style="margin:5px 0 0; font-size:14px;">PWOA Website</p>
            </td>
        </tr>

        {{-- BODY --}}
        <tr>
            <td style="padding:25px; color:#333333;">

                <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;">

                    <tr>
                        <td style="background:#f1f3f6;"><strong>Inquiry Type</strong></td>
                        <td>{{ $data['inquiry_type'] }}</td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafc;"><strong>Name</strong></td>
                        <td>{{ $data['name'] }}</td>
                    </tr>

                    <tr>
                        <td style="background:#f1f3f6;"><strong>Email</strong></td>
                        <td>{{ $data['email'] }}</td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafc;"><strong>Phone</strong></td>
                        <td>{{ $data['phone'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td style="background:#f1f3f6;"><strong>Company</strong></td>
                        <td>{{ $data['company'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafc;"><strong>Subject</strong></td>
                        <td>{{ $data['subject'] }}</td>
                    </tr>

                </table>

                {{-- MESSAGE --}}
                <div style="margin-top:20px;">
                    <strong>Message:</strong>
                    <div style="margin-top:10px; padding:15px; background:#f9fafc; border-left:4px solid #1f3c88;">
                        {{ $data['message'] }}
                    </div>
                </div>

            </td>
        </tr>

        {{-- FOOTER --}}
        <tr>
            <td style="background:#f1f3f6; padding:15px; text-align:center; font-size:12px; color:#777;">
                This message was sent from the PWOA Contact Form.
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>

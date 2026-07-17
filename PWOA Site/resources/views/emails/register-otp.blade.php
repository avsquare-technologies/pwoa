<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email Address - PWOA</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa; padding:20px;">
<tr>
<td align="center">

    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">

        {{-- HEADER --}}
        <tr>
            <td style="background:#0095d7; padding:30px 20px; text-align:center; color:#ffffff;">
                <h2 style="margin:0; font-size: 24px; font-weight: bold; letter-spacing: 0.5px;">🔐 Email Verification Code</h2>
                <p style="margin:5px 0 0; font-size:14px; opacity:0.9;">Pressure Washers of America</p>
            </td>
        </tr>

        {{-- BODY --}}
        <tr>
            <td style="padding:30px 25px; color:#333333; line-height:1.6;">
                <h3 style="margin-top:0; color:#0f172a;">Hello {{ $data['name'] }},</h3>
                
                <p style="margin-bottom:20px; color:#475569;">
                    Thank you for starting your registration with Pressure Washers of America (PWOA).
                </p>

                <p style="margin-bottom:20px; color:#475569;">
                    Please use the following One-Time Password (OTP) to verify your email address and complete your account registration.
                </p>

                <div style="font-size: 36px; font-weight: 800; background: #eff6ff; color: #0095d7; padding: 20px; border-radius: 10px; border: 2px dashed #0095d7; text-align: center; letter-spacing: 8px; margin: 25px 0; font-family: 'Courier New', Courier, monospace;">
                    {{ $data['otp'] }}
                </div>

                <p style="margin-bottom:20px; color:#ef4444; font-weight: bold; font-size: 14px; text-align: center;">
                    This verification code is valid for 15 minutes.
                </p>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;" />

                <p style="margin-bottom:0; font-size:13px; color:#64748b; text-align:center;">
                    If you did not initiate this registration request, you can safely ignore this email.
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

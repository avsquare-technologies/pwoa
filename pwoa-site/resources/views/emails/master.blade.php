<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Pressure Washers of America' }}</title>
</head>

<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa; padding:20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background:#ffffff; padding:30px 20px; text-align:center; border-bottom:1px solid #e2e8f0;">
                            <img src="{{ $message->embed(public_path('assets/pwoa-logo.png')) }}" alt="PWOA Logo" style="height:55px; max-height:55px; display:inline-block; margin-bottom:12px; border:0;">
                            <h2 style="margin:0; font-size: 22px; font-weight: bold; color:#1e293b; letter-spacing: 0.5px;">
                                {{ $subject ?? 'Message from PWOA' }}</h2>
                            <p style="margin:5px 0 0; font-size:13px; color:#64748b;">Pressure Washers of America</p>
                        </td>
                    </tr>

                    {{-- BODY --}}
                    <tr>
                        <td style="padding:30px 25px; color:#333333; line-height:1.6;">
                            {!! $content !!}
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background:#0f172a; padding:25px; text-align:center; font-size:12px; color:#94a3b8;">
                            <p style="margin:0 0 8px;"><strong>Pressure Washers of America (PWOA)</strong></p>
                            <p style="margin:0; opacity:0.8;">Connecting and elevating pressure washing professionals
                                nationwide.</p>
                            <p style="margin:10px 0 0; opacity:0.6;"><a href="{{ url('/') }}"
                                    style="color:#94a3b8; text-decoration:none;">Visit our website</a></p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
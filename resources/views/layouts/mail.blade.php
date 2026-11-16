<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #050d1a; font-family: sans-serif;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 480px; background-color: #0a1e3f; border-radius: 16px; padding: 32px; color: #ffffff;">
        
        <tr>
            <td>
                @hasSection('badge')
                    <span style="background: #1b3257; color: #d99b00; font-size: 11px; font-weight: bold; padding: 6px 12px; border-radius: 20px; text-transform: uppercase;">
                        @yield('badge')
                    </span>
                @endif

                @hasSection('heading')
                    <h2 style="font-size: 22px; margin: 20px 0 10px 0;">@yield('heading')</h2>
                @endif
            </td>
        </tr>

        <tr>
            <td style="padding-top: 10px;">
                @yield('content')
            </td>
        </tr>

        <tr>
            <td style="padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 30px; text-align: center;">
                <p style="color: #61799b; font-size: 12px; line-height: 1.5; margin: 0 0 8px 0;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
                <p style="color: #61799b; font-size: 11px; margin: 0;">
                    If you didn't request this email, please ignore it or contact our support team.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
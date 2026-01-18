<!DOCTYPE html>
<html lang="vi">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Xác thực tài khoản ZENTRA</title>
    <style>
        /* CSS Reset cho Email Client */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #000000; }
        
        /* Responsive Mobile */
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; }
            .fluid { max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <!-- Center Wrapper -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #09090b;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                
                <!-- Email Container (Max 600px) -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" class="email-container" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 12px; overflow: hidden; box-shadow: 0 0 40px rgba(220, 38, 38, 0.15);">
                    
                    <!-- Header: Logo -->
                    <tr>
                        <td align="center" style="padding: 40px 0; background-color: #101012; border-bottom: 3px solid #dc2626;">
                            <!-- Thay link logo online của bro vào đây nếu có, không thì dùng Text -->
                             <h1 style="margin: 0; font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: 2px; text-transform: uppercase;">
                                ZENTRA <span style="color: #dc2626;">GROUP</span>
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 50px;">
                            
                            <!-- Icon -->
                            <div style="text-align: center; margin-bottom: 30px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/646/646094.png" alt="Email" width="64" style="filter: invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%); opacity: 0.8;">
                            </div>

                            <!-- Greeting -->
                            <h2 style="color: #ffffff; margin: 0 0 20px 0; font-size: 22px; text-align: center;">
                                Xin chào, <span style="color: #ef4444;">{{ $user->name }}</span>!
                            </h2>

                            <!-- Text -->
                            <p style="color: #a1a1aa; font-size: 16px; line-height: 26px; margin: 0 0 30px 0; text-align: center;">
                                Cảm ơn bạn đã tham gia hệ sinh thái <strong>ZENTRA</strong>. <br>
                                Bạn chỉ còn một bước nữa để truy cập kho nguyên liệu MMO và các công cụ phân tích YouTube đỉnh cao.
                            </p>

                            <!-- Button (Vùng bấm được rộng) -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p>Mã xác thực (OTP) của bạn là: <strong style="font-size: 24px; color: #ef4444;">{{ $otp }}</strong></p>
                                    </td>
                                    <p>Mã này sẽ hết hạn sau 60 giây.</p>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #000000; padding: 30px; text-align: center;">
                            <p style="color: #52525b; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} ZENTRA Analytics Inc.<br>
                                Hệ thống tự động, vui lòng không trả lời email này.
                            </p>
                            <div style="margin-top: 15px;">
                                <a href="#" style="color: #71717a; text-decoration: none; margin: 0 10px; font-size: 12px;">Trang chủ</a>
                                <a href="#" style="color: #71717a; text-decoration: none; margin: 0 10px; font-size: 12px;">Hỗ trợ</a>
                            </div>
                        </td>
                    </tr>

                </table>
                <!-- End Email Container -->

            </td>
        </tr>
    </table>

</body>
</html>
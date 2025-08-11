<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="20" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px;">
                    <tr>
                        <td align="center" style="font-size: 24px; font-weight: bold; color: #333;">
                            Permintaan Reset Password
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px; color: #555;">
                            Halo <strong><?= $username ?? ""; ?></strong>,<br><br>
                            Kami menerima permintaan untuk mereset password akun Anda.<br>
                            Jika Anda tidak melakukan permintaan ini, abaikan email ini.<br><br>
                            Klik tombol di bawah untuk mereset password Anda:
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <a href="<?= $resetUrl ?? ""; ?>" style="background-color: #007bff; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                                Reset Password Sekarang
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #555; padding-top: 20px;">
                            Atau salin dan tempel URL berikut ke browser Anda jika tombol tidak berfungsi:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #007bff; word-break: break-all;">
                            <a href="<?= $resetUrl ?? ""; ?>" style="color: #007bff;"><?= $resetUrl ?? ""; ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #888;">
                            Tautan ini hanya berlaku selama 1 jam untuk menjaga keamanan akun Anda.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #888;">
                            Jika Anda membutuhkan bantuan lebih lanjut, hubungi tim dukungan kami.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #999; text-align: center;">
                            &copy; 2025 - <a href="https://example.com" style="color: #999; text-decoration: none;">example.com</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
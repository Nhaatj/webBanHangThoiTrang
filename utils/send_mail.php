<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

function sendEmail($to, $subject, $content) {
    $mail = new PHPMailer(true);

    try {
        // Cấu hình Server
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // --- THAY ĐỔI THÔNG TIN CỦA BẠN Ở ĐÂY ---
        $mail->Username   = 'nguyenminhnhat281004@gmail.com'; // Email của bạn
        $mail->Password   = 'elao ggzq qecp dfgk'; // Mật khẩu ứng dụng (App Password)
        // ----------------------------------------
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Người gửi và người nhận
        $mail->setFrom('nguyenminhnhat281004@gmail.com', 'Thời Trang M&N');
        $mail->addAddress($to);

        // Nội dung
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; // Gửi thất bại: $mail->ErrorInfo
    }
}
?>
<?php
require_once('../../database/config.php');
require_once('../../database/dbhelper.php');
require_once('../../utils/utility.php');
require_once('../../utils/send_mail.php');

$email = getPost('email');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập email!']);
    die();
}

// 1. Kiểm tra email có tồn tại không
$sql = "SELECT * FROM User WHERE email = '$email' AND deleted = 0";
$user = executeResult($sql, true);

if ($user == null) {
    echo json_encode(['status' => 'error', 'message' => 'Email chưa được đăng ký!']);
    die();
}

// 2. Tạo Token và lưu vào DB
$token = md5(time() . $email); // Tạo mã ngẫu nhiên
$expiry = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Hết hạn sau 15 phút

// Update token vào bảng User cho tài khoản này
$id = $user['id'];
$sql = "UPDATE User SET reset_token = '$token', reset_token_exp = '$expiry' WHERE id = $id";
execute($sql);

// 3. Gửi Email
// Lưu ý: Sửa đường dẫn localhost dưới đây cho đúng với đường dẫn máy bạn
$link = "http://localhost/webBanHangThoiTrang/admin/authen/reset_password.php?token=" . $token;

$subject = "Yêu cầu đặt lại mật khẩu";
$body = "
    <h3>Chào " . $user['fullname'] . ",</h3>
    <p>Bạn vừa yêu cầu đặt lại mật khẩu trên hệ thống.</p>
    <p>Vui lòng click vào link dưới đây để đặt lại mật khẩu (Link hết hạn sau 15 phút):</p>
    <p><a href='$link'>Click vào đây để đặt lại mật khẩu</a></p>
    <p>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
";

$sent = sendEmail($email, $subject, $body);

if ($sent) {
    echo json_encode(['status' => 'success', 'message' => 'Hãy kiểm tra tin nhắn email của bạn để đặt lại mật khẩu!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không thể gửi email. Vui lòng thử lại sau.']);
}
?>
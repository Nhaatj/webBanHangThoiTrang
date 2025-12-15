<!-- File này giải quyết logic cho login.php -->

<?php
$fullname = $email = $msg = '';

if (!empty($_POST)) {
    $email = getPost('email');
    $pwd = getPost('password');
    $pwd = getSecurityMD5($pwd);

    $sql = "select * from User where email = '$email' and password = '$pwd' and deleted = 0";
    $userExist = executeResult($sql, isSingle: true);
    if ($userExist == null) {
        $msg = 'Đăng nhập thất bại!<br>Vui lòng kiểm tra lại Email hoặc Mật Khẩu.';
    } else {
        //login thành công
        $token = getSecurityMD5($userExist['email'] . time());
        setcookie('token', $token, time() + 7 * 24 * 60 * 60, '/');
        $created_at = date('Y-m-d H:i:s');
        $userId = $userExist['id'];

        $_SESSION['user'] = $userExist;

        $sql = "insert into Tokens (user_id, token, created_at) values ('$userId','$token', '$created_at')";
        execute($sql);

        // Phân quyền hướng đi sau khi đăng nhập
        if ($userExist['role_id'] == 1) {
            header('Location: ../index.php'); // Admin vào trang quản trị
        } else {
            header('Location: ../../index.php'); // Khách hàng về trang chủ Shop
        }
        die();
    }
}
?>
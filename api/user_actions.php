<?php
session_start();
require_once('../utils/utility.php');
require_once('../database/dbhelper.php');

$action = getPost('action'); // Dùng getPost hoặc getGet tùy trường hợp

// Xử lý Request
if ($action == 'login') {
    doLogin();
} elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
    doLogout();
} elseif ($action == 'update_profile') {
    updateProfile();
}

function doLogin() {
    $email = getPost('email');
    $password = getPost('password');
    $pwd = getSecurityMD5($password);

    $sql = "select * from User where email = '$email' and password = '$pwd' and deleted = 0";
    $user = executeResult($sql, true);

    if ($user != null) {
        // Lưu session
        $_SESSION['user'] = $user;
        
        // Đồng bộ giỏ hàng từ Session vào DB và ngược lại
        syncCartLogin($user['id']);
        // ---------------------

        // Lưu token để tự động đăng nhập lần sau (nếu cần)
        $token = getSecurityMD5($user['email'] . time());
        setcookie('token', $token, time() + 7 * 24 * 60 * 60, '/');
        $created_at = date('Y-m-d H:i:s');
        $userId = $user['id'];
        $sql = "insert into Tokens (user_id, token, created_at) values ('$userId','$token', '$created_at')";
        execute($sql);

        echo 'success'; // Trả về success cho AJAX
    } else {
        echo 'Thông tin đăng nhập không chính xác!';
    }
}

function doLogout() {
    $token = getCookie('token');
    setcookie('token', '', time() - 100, '/');
    session_destroy();
    
    // Quay lại trang trước đó
    if(isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: ../index.php');
    }
}

function updateProfile() {
    $user = getUserToken();
    if ($user == null) die();

    $id = $user['id'];
    $fullname = getPost('fullname');
    $phone_number = getPost('phone_number');
    $address = getPost('address');
    $updated_at = date("Y-m-d H:i:s");

    $sql = "update User set fullname = '$fullname', phone_number = '$phone_number', address = '$address', updated_at = '$updated_at' where id = $id";
    execute($sql);

    // Cập nhật lại Session để hiển thị ngay tên mới
    $sql = "select * from User where id = $id";
    $newUser = executeResult($sql, true);
    $_SESSION['user'] = $newUser;

    header('Location: ../account.php?msg=success');
}
?>
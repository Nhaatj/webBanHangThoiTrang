<?php
session_start();
require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');

$user = getUserToken();
if($user != null) {
    header('Location: ../../index.php');
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quên mật khẩu</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="../../assets/photos/logo.jpg" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body style="background-image: url(../../assets/photos/ecommerce2.jpg); background-size: cover;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5" style="margin-top: 100px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.2);">
                <h3 class="text-center">KHÔI PHỤC MẬT KHẨU</h3>
                <P style="color: grey"><i>Đây là trang Khôi phục mật khẩu</i></P>
            </div>
        </div>
    </div>
</body>
</html>
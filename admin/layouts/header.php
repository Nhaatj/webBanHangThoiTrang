<?php
session_start();
require_once($baseUrl.'../utils/utility.php');
require_once($baseUrl.'../database/dbhelper.php');

$user = getUserToken();

// 1. Chưa đăng nhập -> Đá về trang login
if($user == null) {
    header('Location: '.$baseUrl.'authen/login.php');
    die();
}

// 2. Đã đăng nhập nhưng KHÔNG PHẢI ADMIN (role_id != 1) -> Đá về trang chủ Shop
// Giả định: role_id = 1 là Admin, role_id = 2 là User/Khách hàng
if($user['role_id'] != 1) {
    // $baseUrl đang là đường dẫn tương đối trong admin (vd: '../' hoặc '')
    // Nối thêm '../index.php' để ra thư mục gốc (Trang chủ Shop)
    header('Location: '.$baseUrl.'../index.php');
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?=$title?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="<?=$baseUrl?>../assets/photos/logo.jpg" />

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="<?=$baseUrl?>../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    
    <style>
        .border-top{
            border-bottom: 1px solid rgba(58, 75, 92, 0.2);
            margin: 0;
        }

        .logout {
            padding: 0 1rem;
        }
    </style>

</head>
<body>
<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="<?=$baseUrl?>../index.php">M&N</a>
    <!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Tìm kiếm" aria-label="Search"> -->
    
</nav>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky d-flex flex-column" style="height: calc(100vh - 48px);">
                <ul class="nav flex-column" style="padding-bottom: .75rem; font-size: 1rem">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?=$baseUrl?>">
                            <i class="bi bi-house-fill"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$baseUrl?>category">
                            <i class="bi bi-folder"></i>
                            Danh Mục Sản Phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$baseUrl?>product">
                            <i class="bi bi-file-earmark-text"></i>
                            Sản Phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$baseUrl?>order">
                            <i class="bi bi-minecart"></i>
                            Đơn Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$baseUrl?>feedback">
                            <i class="bi bi-question-circle-fill"></i>
                            Phản Hồi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$baseUrl?>user">
                            <i class="bi bi-people-fill"></i>
                            Người Dùng
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav mt-auto" style="padding-top: .75rem; font-size: 1rem;"> 
                    <div class="border-top"></div>
                    <li class="nav-item logout">
                        <a class="nav-link" href="<?=$baseUrl?>authen/logout.php" style="color: #000;">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <!-- hiển thị từng chức năng của trang quản trị START -->
<?php
session_start();
require_once('utils/utility.php');
require_once('database/dbhelper.php');

$sql = "select * from Category";
$menuItems = executeResult($sql);

$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id order by Product.updated_at desc limit 0,10";
$latestItems = executeResult(sql: $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Shop Thời Trang M&N</title>
    <link rel="shortcut icon" href="assets/photos/logo.jpg" />
    
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
 
    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/home.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <style type="text/css">
        .nav li {
            text-transform: uppercase;
            /* margin-top: 10px; */
        }
        .nav li a {
            color: black;
            font-weight: 500;
        }
        .nav {
            display: flex;
            align-items: center;
        }
        .carousel-inner img {
            height: 100%;
            width: 100%;
        }
        .product-item:hover {
            background-color: #f5f6f7;
            cursor: pointer;
        }
        .product-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<!-- Menu START -->
    <header>
        <div class="header-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-2 col-3">
                        <a href="index.php" class="logo">
                            <img src="assets/photos/logo.jpg" alt="M&N Shop">
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-5">
                        <form action="index.php" method="get">
                            <div class="search-container">
                                <input type="text" name="search" placeholder="Bạn đang tìm gì...">
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4 col-4">
                        <div class="header-actions">
                            <a href="contact.php" class="action-item">
                                <ion-icon name="call-outline" style="width: 24px; height: 24px;"></ion-icon>
                                <span>Liên hệ</span>
                            </a>
                            
                            <a href="admin/authen/login.php" class="action-item">
                                <!-- <i class="fa fa-user"></i> -->
                                <!-- <img src="https://cdn.hstatic.net/themes/1000253775/1001427348/14/user-account.svg?v=293" width="24px" height="24px"> -->
                                <ion-icon name="person-outline" style="width: 24px; height: 24px;"></ion-icon>
                                <span>Đăng nhập</span>
                            </a>

                            <?php
                            if(!isset($_SESSION['cart'])){
                                $_SESSION['cart'] = [];
                            }
                            $count = 0;
                            foreach($_SESSION['cart'] as $item) {
                                $count += $item['num'];
                            }
                            ?>
                            <!-- GIỎ HÀNG -->
                            <a href="cart.php" class="action-item">
                                <!-- <img src="https://cdn.hstatic.net/themes/1000253775/1001427348/14/shopping-cart.svg?v=293" width="24px" height="24px"> -->
                                <ion-icon name="cart-outline" style="width: 24px; height: 24px;"></ion-icon>
                                <span>Giỏ hàng</span>
                                <span class="cart-count"><?= $count ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-menu">
            <div class="container">
                <ul class="nav-list">
                    <li class="nav-item-custom">
                        <a href="category.php"><i class="fa fa-search" style="color: #007bff; margin-right: 5px;"></i> SẢN PHẨM<i class="badge-new fa fa-star"></i></a>
                    </li>

                    <?php foreach($menuItems as $item): ?>
                        <li class="nav-item-custom">
                            <a href="category.php?id=<?=$item['id']?>">
                                <?=$item['name']?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <li class="nav-item-custom">
                        <a href="#">TIN THỜI TRANG</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
<!-- Menu STOP -->
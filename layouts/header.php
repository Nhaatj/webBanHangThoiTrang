<?php
session_start();
require_once('utils/utility.php');
require_once('database/dbhelper.php');

$sql = "select * from Category";
$menuItems = executeResult($sql);

$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id order by Product.updated_at desc limit 0,8";
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

    <link rel="stylesheet" href="assets/css/footer.css">
    
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
    <ul class="nav">
        <li style="margin-top: 0px !important;"><img src="assets/photos/logo.jpg" style="height: 80px;"></li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                Trang Chủ
            </a>
        </li>
        <?php
        foreach($menuItems as $item) {
            echo '<li class="nav-item">
                    <a class="nav-link" href="category.php?id='.$item['id'].'">
                        '.$item['name'].'
                    </a>
                </li>';
        }
        ?>
        <li class="nav-item">
            <a class="nav-link" href="contact.php">
                Liên Hệ
            </a>
        </li>
    </ul>
<!-- Menu STOP -->
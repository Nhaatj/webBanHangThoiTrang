<?php
session_start();
require_once('utils/utility.php');
require_once('database/dbhelper.php');

$sql = "select * from Category";
$menuItems = executeResult($sql);

$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id order by Product.updated_at desc limit 0,10";
$latestItems = executeResult(sql: $sql);

$user = getUserToken();
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
    <link rel="stylesheet" href="assets/css/main.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
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
                            
                            <div class="action-item" style="cursor: pointer; position: relative;" id="user-action-btn">
                                <!-- <i class="fa fa-user"></i> -->
                                <!-- <img src="https://cdn.hstatic.net/themes/1000253775/1001427348/14/user-account.svg?v=293" width="24px" height="24px"> -->
                                <ion-icon name="person-outline" style="width: 24px; height: 24px;"></ion-icon>
                                <?php if ($user == null) { ?>
                                    <span>Đăng nhập</span>

                                    <div class="login-dropdown" id="loginDropdown" onclick="event.stopPropagation()">
                                        <h5 style="text-align: center; font-weight: bold; margin-bottom: 5px;">ĐĂNG NHẬP TÀI KHOẢN</h5>
                                        <p style="text-align: center; font-size: 13px; color: #666; margin-bottom: 20px;">Nhập email và mật khẩu của bạn:</p>
                                        
                                        <div id="login_msg" style="color: red; font-size: 13px; text-align: center; margin-bottom: 10px;"></div>

                                        <form id="popupLoginForm">
                                            <input type="email" class="form-control" id="popup_email" placeholder="Nhập email hoặc số điện thoại" required>
                                            <input type="password" class="form-control" id="popup_pwd" placeholder="Mật khẩu" required>
                                            <button type="submit" class="btn-login-submit">ĐĂNG NHẬP</button>
                                        </form>

                                        <div style="margin-top: 15px; font-size: 13px;">
                                            Khách hàng mới? <a href="admin/authen/register.php" style="color: #000; font-weight: bold;">Tạo tài khoản</a><br>
                                            Quên mật khẩu? <a href="#" style="color: #000; font-weight: bold;">Khôi phục mật khẩu</a>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <div class="user-name-container">
                                        <span class="user-name-scroll <?= (strlen($user['fullname']) > 8) ? 'scrolling' : '' ?>">
                                            <?= $user['fullname'] ?>
                                        </span>
                                    </div>

                                    <div class="user-dropdown-menu" id="userDropdown">
                                        <div class="user-dropdown-header">
                                            <?= $user['fullname'] ?>
                                        </div>
                                        <?php if($user['role_id'] == 1) { ?>
                                            <a href="admin/" class="user-dropdown-item">
                                                <i class="fa fa-cog"></i> Quản trị
                                            </a>
                                        <?php } ?>
                                        <a href="account.php" class="user-dropdown-item">
                                            <i class="fa fa-user"></i> Tài khoản
                                        </a>
                                        <a href="api/user_actions.php?action=logout" class="user-dropdown-item">
                                            <i class="fa fa-sign-out-alt"></i> Đăng xuất
                                        </a>
                                    </div>
                                <?php } ?>                                
                            </div>

                            <?php
                            if(!isset($_SESSION['cart'])){
                                $_SESSION['cart'] = [];
                            }
                            $count = 0;
                            foreach($_SESSION['cart'] as $item) {
                                $count += $item['num'];
                                // Kiểm tra ngay sau khi cộng
                                if($count > 999) {
                                    $count = 999;
                                    break; // Dừng vòng lặp luôn cho đỡ tốn tài nguyên
                                }
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

    <script>
        $(document).ready(function() {
            // --- Xử lý kéo thả chuột từ trong ra ngoài ---
            var isInteractInside = false;

            // Khi nhấn chuột xuống (mousedown) bên trong 2 khung này -> Đánh dấu là đang tương tác bên trong
            $('#loginDropdown, #userDropdown').mousedown(function(e) {
                isInteractInside = true;
            });

            // Khi nhả chuột (mouseup) ở bất kỳ đâu -> Hủy đánh dấu (dùng setTimeout để chờ sự kiện click chạy xong trước)
            $(document).mouseup(function() {
                setTimeout(function() {
                    isInteractInside = false;
                }, 100);
            });
            // ---------------------------------------------

            // 1. Xử lý Toggle Popup cho cả Login và User Menu
            $('#user-action-btn').click(function(e) {
                // Kiểm tra xem đang là Login Popup hay User Dropdown
                if ($('#userDropdown').length > 0) {
                    // Đã đăng nhập -> Toggle User Menu
                    $('#userDropdown').toggle();
                } else {
                    // Chưa đăng nhập -> Toggle Login Popup và Overlay
                    $('#loginDropdown').toggle();
                    $('#page-overlay').toggle();
                }
                e.stopPropagation();
            });

            // 2. Click ra ngoài thì đóng popup
            $(document).click(function() {
                // Nếu hành động bắt đầu từ bên trong (kéo thả chuột từ trong ra ngoài) -> KHÔNG đóng
                if (isInteractInside) return;

                $('#userDropdown').hide();
                $('#loginDropdown').hide();
                $('#page-overlay').hide();
            });

            // 3. Ngăn sự kiện click bên trong popup làm đóng popup
            // (Đã xử lý bằng onclick="event.stopPropagation()" trong HTML ở trên, nhưng thêm cho chắc)
            $('#loginDropdown, #userDropdown').click(function(e){
                e.stopPropagation();
            });

            // 4. Xử lý Submit Form Login (Ajax)
            $('#popupLoginForm').submit(function(e) {
                e.preventDefault();
                var email = $('#popup_email').val();
                var password = $('#popup_pwd').val();

                $.post('api/user_actions.php', {
                    'action': 'login',
                    'email': email,
                    'password': password
                }, function(data) {
                    if(data == 'success') {
                        location.reload(); 
                    } else {
                        $('#login_msg').text(data);
                    }
                })
            });
        });
    </script>
<!-- Menu STOP -->
 
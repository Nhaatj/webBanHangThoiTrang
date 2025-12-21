<?php
session_start();
require_once('../utils/utility.php');
require_once('../database/dbhelper.php');

// Lấy tham số từ URL
$action = getGet('action');
$index = getGet('index'); // Vị trí sản phẩm trong mảng $_SESSION['cart']

// Kiểm tra xem giỏ hàng và sản phẩm tại vị trí index có tồn tại không
if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$index])) {
    
    $item = $_SESSION['cart'][$index]; // Lấy thông tin item trước khi thao tác
    $productId = $item['id'];
    $size = $item['size'];

    switch ($action) {
        case 'delete':
            // Xóa khỏi Session: xóa phần tử tại vị trí $index và sắp xếp lại chỉ số mảng
            array_splice($_SESSION['cart'], $index, 1);
            break;

            // Xóa khỏi DB (Nếu đã đăng nhập)
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                execute("DELETE FROM Cart WHERE user_id = $userId AND product_id = $productId AND size = '$size'");
            }
        case 'update':
            $delta = getGet('delta'); // Lấy giá trị tăng giảm (+1 hoặc -1)
            $current_qty = $_SESSION['cart'][$index]['num'];
            $new_qty = $current_qty + $delta;

            if ($new_qty <= 0) {
                // LOGIC: Nếu số lượng mới <= 0 -> Xóa luôn sản phẩm
                array_splice($_SESSION['cart'], $index, 1);
                if (isset($_SESSION['user'])) {
                    $userId = $_SESSION['user']['id'];
                    execute("DELETE FROM Cart WHERE user_id = $userId AND product_id = $productId AND size = '$size'");
                }
            } else {
                // Ngược lại -> Cập nhật số lượng mới
                // Update Session
                $_SESSION['cart'][$index]['num'] = $new_qty;

                // Update DB (Nếu đã đăng nhập)
                if (isset($_SESSION['user'])) {
                    $userId = $_SESSION['user']['id'];
                    execute("UPDATE Cart SET num = $new_qty WHERE user_id = $userId AND product_id = $productId AND size = '$size'");
                }
            }
            break;
    }
}

// Xử lý xong thì quay về trang giỏ hàng
header('Location: ../cart.php');
die();
?>
<?php
session_start();
require_once('../utils/utility.php');

// Lấy tham số từ URL
$action = getGet('action');
$index = getGet('index'); // Vị trí sản phẩm trong mảng $_SESSION['cart']

// Kiểm tra xem giỏ hàng và sản phẩm tại vị trí index có tồn tại không
if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$index])) {
    
    switch ($action) {
        case 'delete':
            // Xóa phần tử tại vị trí $index và sắp xếp lại chỉ số mảng
            array_splice($_SESSION['cart'], $index, 1);
            break;

        case 'update':
            $delta = getGet('delta'); // Lấy giá trị tăng giảm (+1 hoặc -1)
            $current_qty = $_SESSION['cart'][$index]['num'];
            $new_qty = $current_qty + $delta;

            if ($new_qty <= 0) {
                // LOGIC: Nếu số lượng mới <= 0 -> Xóa luôn sản phẩm
                array_splice($_SESSION['cart'], $index, 1);
            } else {
                // Ngược lại -> Cập nhật số lượng mới
                $_SESSION['cart'][$index]['num'] = $new_qty;
            }
            break;
    }
}

// Xử lý xong thì quay về trang giỏ hàng
header('Location: ../cart.php');
die();
?>
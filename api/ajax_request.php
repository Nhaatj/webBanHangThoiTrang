<?php
session_start();
require_once('../utils/utility.php');
require_once('../database/dbhelper.php');

$action = getPost('action');

switch ($action) {
  case 'cart':
    addToCart();
    break;
}

function addToCart() {
  $id = getPost('id');
  $num = getPost('num');
  $size = getPost('size'); // 1. Nhận size từ client gửi lên

  if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  $isFind = false;
  for($i = 0; $i < count($_SESSION['cart']); $i++) {
    // Lấy size của sản phẩm đang có trong giỏ (nếu chưa có key size thì mặc định là rỗng)
    $oldSize = isset($_SESSION['cart'][$i]['size']) ? $_SESSION['cart'][$i]['size'] : '';

    // 2. So sánh: Phải trùng cả ID sản phẩm VÀ trùng cả Size thì mới cộng dồn số lượng
    if($_SESSION['cart'][$i]['id'] == $id && $oldSize == $size) {
      $_SESSION['cart'][$i]['num'] += $num;
      $isFind = true;
      break;
    }
  }

  if(!$isFind) {
    $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.id = $id";
    $product = executeResult($sql, true);
    $product['num'] = $num;
    $product['size'] = $size; // 3. Lưu thông tin size vào sản phẩm trong giỏ
    
    $_SESSION['cart'][] = $product;
  }
}

function addCartWithSize(productId) {
    var num = $('input[name=num]').val();
    var size = $('#selected_size').val(); // Lấy giá trị từ input ẩn (đã tạo ở bài trước)

    // Kiểm tra: Nếu sản phẩm có hiển thị nút chọn size mà khách chưa chọn
    if ($('.size-btn').length > 0 && size == '') {
        alert('Vui lòng chọn kích thước sản phẩm!');
        return; // Dừng lại, không gửi đi
    }

    // Gọi hàm gốc ở footer để gửi đi
    addCart(productId, num, size);
}
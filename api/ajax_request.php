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

  // 1. Xử lý Session
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
    $sql = "select * from Product where id = $id";
    $product = executeResult($sql, true);
    $product['num'] = $num;
    $product['size'] = $size; // 3. Lưu thông tin size vào sản phẩm trong giỏ

    $_SESSION['cart'][] = $product;
  }

  // 2. Xử lý Database (NẾU ĐÃ ĐĂNG NHẬP) 
  if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    
    // Kiểm tra xem món này đã có trong DB chưa
    $sql = "SELECT id, num FROM Cart WHERE user_id = $userId AND product_id = $id AND size = '$size'";
    $existingItem = executeResult($sql, true);

    if ($existingItem) {
      // Ép kiểu về số nguyên (int) để đảm bảo phép cộng ra số, không ra mảng
      $currentNum = (int)$existingItem['num'];
      $addNum = (int)$num;
      $newNum = $currentNum + $addNum;
      
      $cartId = $existingItem['id'];      
      execute("UPDATE Cart SET num = $newNum WHERE id = $cartId");
    } else {
      execute("INSERT INTO Cart (user_id, product_id, num, size) VALUES ($userId, $id, $num, '$size')");
    }
  }
}
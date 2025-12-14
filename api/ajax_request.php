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

  if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  $isFind = false;
  for($i = 0; $i < count($_SESSION['cart']); $i++) {
    if($_SESSION['cart'][$i]['id'] == $id) {
      $_SESSION['cart'][$i]['num'] += $num;
      $isFind = true;
      break;
    }
  }

  if(!$isFind) {
    $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.id = $id";
    $product = executeResult($sql, true);
    $product['num'] = $num;
    $_SESSION['cart'][] = $product;
  }
}
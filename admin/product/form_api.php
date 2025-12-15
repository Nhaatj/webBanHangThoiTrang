<?php
session_start();
require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');

$user = getUserToken();
// 1. Chưa đăng nhập -> Dừng
if ($user == null) {
    die();
}

// 2. Đã đăng nhập nhưng không phải Admin -> Dừng ngay
if ($user['role_id'] != 1) {
    die(); // Hoặc echo 'Bạn không có quyền thực hiện thao tác này';
}

if (!empty($_POST)) {
  $action = getPost('action');

  switch ($action) {
    case 'delete':
      deleteProduct();
      break;
  }
}

function deleteProduct()
{
  $id = getPost('id');
  $updated_at = date("Y-m-d H:i:s");
  $sql = "update Product set deleted = 1, updated_at = '$updated_at' where id = '$id'";
  execute($sql);
}

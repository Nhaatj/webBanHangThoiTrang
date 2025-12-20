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
    case 'update_status':
      updateStatus();
      break;
  }
}

function updateStatus()
{
  $id = getPost('id');
  $status = getPost('status');

  $received_date_sql = "";
  // Nếu chuyển sang status 2 (Hoàn thành, đã nhận hàng) -> Cập nhật thời gian nhận
  if ($status == 2) {
      $now = date('Y-m-d H:i:s');
      $received_date_sql = ", received_date = '$now'";
  }

  $sql = "update Orders set status = $status $received_date_sql where id = $id";
  execute($sql);
}

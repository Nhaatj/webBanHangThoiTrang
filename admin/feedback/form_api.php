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
    case 'mark_read':
      markReadFeedback();
      break;
    case 'delete':
      deleteFeedback();
      break;
  }
}

function markReadFeedback()
{
  // Nhận chuỗi JSON ids từ client gửi lên
  $idsJson = $_POST['ids'];
  $ids = json_decode($idsJson); // Chuyển về mảng PHP

  if (is_array($ids) && count($ids) > 0) {
    $updated_at = date("Y-m-d H:i:s");
    // Chuyển mảng id thành chuỗi dạng (1, 2, 3) để dùng trong câu lệnh IN
    $idsString = implode(',', $ids);

    // Cập nhật status = 1 (Đã đọc) cho các ID được chọn
    $sql = "update FeedBack set status = 1, updated_at = '$updated_at' where id IN ($idsString)";
    execute($sql);
  }
}

function deleteFeedback()
{
  $idsJson = $_POST['ids'];
  $ids = json_decode($idsJson);

  if (is_array($ids) && count($ids) > 0) {
    $updated_at = date("Y-m-d H:i:s");
    $idsString = implode(',', $ids);

    // CẬP NHẬT: Thêm điều kiện 'and status != 0'
    // Chỉ xóa (ẩn) những phản hồi nào KHÔNG PHẢI là chưa đọc (tức là đã đọc rồi)
    $sql = "update FeedBack set status = 2, updated_at = '$updated_at' where id IN ($idsString) and status != 0";
    execute($sql);
  }
}

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

  // --- HOÀN TỒN KHO KHI HỦY ĐƠN -------
  if ($status == 3) { 
      // Kiểm tra trạng thái hiện tại của đơn hàng để tránh cộng dồn nhiều lần nếu bấm hủy nhiều lần
      $sql_check = "SELECT status FROM Orders WHERE id = $id";
      $currentOrder = executeResult($sql_check, true);

      // Chỉ thực hiện hoàn kho nếu đơn hàng hiện tại CHƯA bị hủy (status khác 3)
      if ($currentOrder && $currentOrder['status'] != 3) {
          
          // 1. Lấy danh sách sản phẩm trong đơn hàng
          $sql_details = "SELECT * FROM Order_Details WHERE order_id = $id";
          $order_details = executeResult($sql_details);

          // 2. Lặp qua từng sản phẩm để cộng lại kho
          foreach ($order_details as $item) {
              $product_id = $item['product_id'];
              $num = $item['num'];
              $size = $item['size'];

              if (!empty($size)) {
                  // TH1: Có size -> Cộng lại bảng Product_Size và Product
                  $sql_restore_size = "UPDATE Product_Size SET inventory_num = inventory_num + $num 
                                       WHERE product_id = $product_id AND size_name = '$size'";
                  execute($sql_restore_size);

                  $sql_restore_total = "UPDATE Product SET inventory_num = inventory_num + $num 
                                        WHERE id = $product_id";
                  execute($sql_restore_total);
              } else {
                  // TH2: Không size -> Chỉ cộng lại bảng Product
                  $sql_restore_total = "UPDATE Product SET inventory_num = inventory_num + $num 
                                        WHERE id = $product_id";
                  execute($sql_restore_total);
              }
          }
      }
  }
  // ---------------------------------------------

  $received_date_sql = "";
  // Nếu chuyển sang status 2 (Hoàn thành, đã nhận hàng) -> Cập nhật thời gian nhận
  if ($status == 2) {
      $now = date('Y-m-d H:i:s');
      $received_date_sql = ", received_date = '$now'";
  }

  $sql = "update Orders set status = $status $received_date_sql where id = $id";
  execute($sql);
}

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
        deleteCategory();
        break;
    }
}

function deleteCategory(){
    $id = getPost('id');

    $sql = "select count(*) as total from Product where category_id = $id and deleted = 0";
    $data = executeResult($sql, true);
    $total = $data['total'];
    if($total > 0) {
        echo 'Danh mục đang chứa sản phẩm, không thể xóa!';
        die();
    }

    $sql = "delete from Category where id = $id";
    execute($sql);
}

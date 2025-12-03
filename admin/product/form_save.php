<?php
if (!empty($_POST)) {
    $id = getPost('id');
    $title = getPost('title');
    $price = getPost('price');
    $discount = getPost('discount');

    // 1. Thử lấy file upload
    $thumbnail = moveFile('thumbnail');

    // 2. Nếu không có file upload, thử lấy từ ô nhập URL
    if (empty($thumbnail)) {
        $thumbnail = getPost('thumbnail_url');
    }

    $description = getPost('description');
    $category_id = getPost('category_id');
    $created_at = $updated_at = date('Y-m-d H:s:i');

    if ($id > 0) {
        //update
        if ($thumbnail != '') {
            $sql = "update Product set thumbnail = '$thumbnail', title = '$title', price = '$price', discount = '$discount', description = '$description', updated_at = '$updated_at', category_id = '$category_id' where id = $id";
        } else {
            $sql = "update Product set title = '$title', price = '$price', discount = '$discount', description = '$description', updated_at = '$updated_at', category_id = '$category_id' where id = $id";
        }

        execute($sql);
        header('Location: index.php');
        die();
    } else {
        // insert
        $sql = "insert into Product(thumbnail, title, price, discount, description, category_id, updated_at, created_at, deleted) values ('$thumbnail', '$title', '$price', '$discount', '$description', '$category_id', '$updated_at', '$created_at', 0)";
        execute($sql);

        header('Location: index.php');
        die();
    }
}

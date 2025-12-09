<?php
if(!empty($_POST)) {
    $id = getPost("id");
    $name = getPost(key: "name");
    // 1. Thử lấy file upload
    $banner = moveFile('banner');
    // 2. Nếu không có file upload, thử lấy từ ô nhập URL
    if (empty($banner)) {
        $banner = getPost('banner_url');
    }

    if ($id > 0) {
        //update
        if ($banner != '') {
            $sql = "update Category set name = '$name', banner = '$banner' where id = $id";
        } else {
            $sql = "update Category set name = '$name' where id = $id";
        }
        execute($sql);
        header('Location: index.php');
        die();
    } else {
        //insert
        $sql = "insert into Category(name, banner) values ('$name', '$banner')";
        execute($sql);
    }
    header('Location: index.php');
    die();
}
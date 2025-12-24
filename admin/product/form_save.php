<?php
if (!empty($_POST)) {
    $id = getPost('id');
    $title = getPost('title');
    $price = getPost('price');
    $discount = getPost('discount');
    $inventory_num = getPost('inventory_num');

    // 1. Thử lấy file upload
    $thumbnail = moveFile('thumbnail');

    // 2. Nếu không có file upload, thử lấy từ ô nhập URL
    if (empty($thumbnail)) {
        $thumbnail = getPost('thumbnail_url');
    }

    $description = getPost('description');
    $category_id = getPost('category_id');
    $created_at = $updated_at = date('Y-m-d H:s:i');

    // 1. Logic lưu sản phẩm cơ bản (Product table)
    // Lưu ý: Tạm thời lưu inventory_num = 0, lát nữa sẽ cập nhật lại sau khi tính tổng size
    if ($id > 0) {
        //update
        if ($thumbnail != '') {
            $sql = "update Product set thumbnail = '$thumbnail', title = '$title', price = '$price', discount = '$discount', description = '$description', updated_at = '$updated_at', category_id = '$category_id' where id = $id";
        } else {
            $sql = "update Product set title = '$title', price = '$price', discount = '$discount', description = '$description', updated_at = '$updated_at', category_id = '$category_id' where id = $id";
        }

        execute($sql);
    } else {
        // insert
        $sql = "insert into Product(thumbnail, title, price, discount, description, category_id, updated_at, created_at, deleted, inventory_num) values ('$thumbnail', '$title', '$price', '$discount', '$description', '$category_id', '$updated_at', '$created_at', 0, 0)";
        execute($sql);
        // Lấy ID vừa insert để thêm size
        $sql = "select id from Product order by id desc limit 1";
        $item = executeResult($sql, true);
        $id = $item['id'];
    }

    // 2. Logic lưu Size (Product_Size table)
    // Xóa size cũ để thêm lại (cách đơn giản nhất để xử lý update/delete)
    execute("DELETE FROM Product_Size WHERE product_id = $id");

    $totalInventory = 0;
    $hasSize = false;

    if (isset($_POST['size_names']) && isset($_POST['size_quantities'])) {
        $names = $_POST['size_names'];
        $quants = $_POST['size_quantities'];

        for ($i = 0; $i < count($names); $i++) {
            $sName = fixSqlInject($names[$i]);
            $sQty = (int)$quants[$i];
            
            if ($sName != '' && $sQty >= 0) {
                execute("INSERT INTO Product_Size(product_id, size_name, inventory_num) VALUES ($id, '$sName', $sQty)");
                $totalInventory += $sQty;
                $hasSize = true;
            }
        }
    }

    // 3. Nếu sản phẩm KHÔNG CÓ size nào được nhập, lấy giá trị từ ô "Tổng tồn kho"
    if (!$hasSize) {
        $totalInventory = getPost('inventory_num');
    }

    // 4. Cập nhật lại tổng tồn kho vào bảng Product
    execute("UPDATE Product SET inventory_num = $totalInventory WHERE id = $id");
    
    echo '<script>window.location.href = "index.php";</script>';
    die();
}

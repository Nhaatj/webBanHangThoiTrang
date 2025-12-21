<?php
//$sql = "insert into Role(name) values ('Admin')";
//$sql = "insert into Role(name) values ('$name')"; => $name = 'Admin => lỗi sql injection => khi join một project thực tế sẽ quy định phải dùng framework có sẵn (vd: Laravel) => fix
//fix core: $name = 'Admin => \'Admin
//fix sql injection => $sql = "câu lệnh sql" (quy tắc để dấu nháy kép "" cho lệnh sql)
function fixSqlInject($sql)
{
    $sql = str_replace('\\', '\\\\', $sql);
    $sql = str_replace('\'', '\\\'', $sql);
    return $sql;
}
// print('\'Admin') = 'Admin
// print('\\') = \
// print('\'') = '
// print('\"') = "

function getGet($key)
{
    $value = '';
    if (isset($_GET[$key])) {
        $value = $_GET[$key];
        $value = fixSqlInject($value);
    }
    return trim($value);
}

function getPost($key)
{
    $value = '';
    if (isset($_POST[$key])) {
        $value = $_POST[$key];
        $value = fixSqlInject($value);
    }
    return trim($value);
}

function getRequest($key)
{
    $value = '';
    if (isset($_REQUEST[$key])) {
        $value = $_REQUEST[$key];
        $value = fixSqlInject($value);
    }
    return trim($value);
}

function getCookie($key)
{
    $value = '';
    if (isset($_COOKIE[$key])) {
        $value = $_COOKIE[$key];
        $value = fixSqlInject($value);
    }
    return trim($value);
}

function getSecurityMD5($pwd)
{
    return md5(md5($pwd) . PRIVATE_KEY);
}

function getUserToken()
{
    // Cách viết khác an toàn hơn:
    // if (!empty($_SESSION['user'])) {
    //     return $_SESSION['user'];
    // }
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    $token = getCookie('token'); //'token' này là tên của một Cookies(F12->Application->Cookies), thuộc trường Name.
    $sql = "select * from Tokens where token = '$token'";
    $item = executeResult($sql, isSingle: true);
    if ($item != null) {
        $userId = $item['user_id']; //'user_id' này là trường user_id từ database
        $sql = "select * from User where id = '$userId' and deleted = 0";
        $item = executeResult($sql, true);
        if ($item != null) {
            $_SESSION['user'] = $item;
            loadCartFromDB($item['id']); // Tự động tải giỏ hàng khi Auto-Login
            return $item;
        }
    }

    return null;
}

function moveFile($key, $rootPath = "../../") {
    if(!isset($_FILES[$key]) || !isset($_FILES[$key]['name']) || $_FILES[$key]['name'] == '') {
        return '';
    }

    $pathTemp = $_FILES[$key]["tmp_name"];
    
    $filename = $_FILES[$key]['name']; 
    //filename -> Có thể làm thêm: bỏ kí tự đặc biệt, khoảng trống, ...vv -> định dạng lại theo chuẩn.

    $newPath = "assets/photos/".$filename;

    move_uploaded_file($pathTemp,$rootPath.$newPath);

    return $newPath;
}

function fixUrl($thumbnail, $rootPath) {
    if(stripos($thumbnail, 'http://') !== false || stripos($thumbnail, 'https://') !== false) { 
        // Nếu là link online thì giữ nguyên
    } else {
        // Nếu là link local thì nối thêm đường dẫn gốc
        $thumbnail = $rootPath.$thumbnail;
    }

    return $thumbnail;
}

// --- LOGIC ĐỒNG BỘ GIỎ HÀNG ---
// 1. Hàm chỉ làm nhiệm vụ: Xóa Session cũ -> Nạp mới từ DB (Dùng khi F5 trang Cart)
function loadCartFromDB($userId) {
    // Nếu lỡ truyền mảng user thì lấy id
    if (is_array($userId) && isset($userId['id'])) {
        $userId = $userId['id'];
    }

    $sql = "SELECT c.num, c.size, p.id, p.title, p.thumbnail, p.price, p.discount 
            FROM Cart c 
            JOIN Product p ON c.product_id = p.id 
            WHERE c.user_id = '$userId'";
    $dbCart = executeResult($sql);

    $_SESSION['cart'] = []; // Xóa sạch session cũ
    foreach ($dbCart as $item) {
        $_SESSION['cart'][] = [
            'id' => $item['id'],
            'title' => $item['title'],
            'thumbnail' => $item['thumbnail'],
            'price' => $item['price'],
            'discount' => $item['discount'],
            'num' => (int)$item['num'],
            'size' => $item['size']
        ];
    }
}

// 2. Hàm đồng bộ khi Đăng nhập: Merge data Session hiện tại với DB -> Gọi hàm load lại toàn bộ data từ DB về Session
function syncCartLogin($userId) {
    if (is_array($userId) && isset($userId['id'])) {
        $userId = $userId['id'];
    }

    // A. Đẩy items từ Session hiện tại vào Database
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $num = (int)$item['num'];
            $size = isset($item['size']) ? $item['size'] : '';

            $sql = "SELECT id, num FROM Cart WHERE user_id = '$userId' AND product_id = '$product_id' AND size = '$size'";
            $existingItem = executeResult($sql, true);

            if ($existingItem) {
                // Cộng dồn
                $newNum = (int)$existingItem['num'] + $num;
                execute("UPDATE Cart SET num = $newNum WHERE id = " . $existingItem['id']);
            } else {
                // Thêm mới
                execute("INSERT INTO Cart (user_id, product_id, num, size) VALUES ('$userId', '$product_id', '$num', '$size')");
            }
        }
    }
    
    // B. Sau khi merge xong, tải lại toàn bộ từ DB về Session
    loadCartFromDB($userId);
}

// 3. Hàm xóa giỏ hàng trong DB sau khi đặt hàng thành công
function clearCartDB($userId) {
    execute("DELETE FROM Cart WHERE user_id = $userId");
}
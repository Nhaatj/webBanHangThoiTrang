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
        $userId = $item['user_id']; //'user' này là trường user từ database
        $sql = "select * from User where id = '$userId' and deleted = 0";
        $item = executeResult($sql, true);
        if ($item != null) {
            $_SESSION['user'] = $item;
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
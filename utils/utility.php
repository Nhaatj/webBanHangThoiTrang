<?php
//$sql = "insert into Role(name) values ('Admin')";
//$sql = "insert into Role(name) values ('$name')"; => $name = 'Admin => lỗi sql injection => khi join một project thực tế sẽ quy định phải dùng framework có sẵn (vd: Laravel) => fix
//fix core: $name = 'Admin => \'Admin
//fix sql injection => $sql = "câu lệnh sql" (quy tắc để dấu nháy kép "" cho lệnh sql)
function fixSqlInject($sql) {
    $sql = str_replace('\\', '\\\\', $sql);
    $sql = str_replace('\'', '\\\'', $sql);
    return $sql;
}
// print('\'Admin') = 'Admin
// print('\\') = \
// print('\'') = '
// print('\"') = "

function getGet($key) {
    $value = '';
    if(isset($_GET[$key])) {
        $value = $_GET[$key];
        $value = fixSqlInject($value);
    }
    return $value;
}

function getPost($key) {
    $value = '';
    if(isset($_POST[$key])) {
        $value = $_POST[$key];
        $value = fixSqlInject($value);
    }
    return $value;
}

function getRequest($key) {
    $value = '';
    if(isset($_REQUEST[$key])) {
        $value = $_REQUEST[$key];
        $value = fixSqlInject($value);
    }
    return $value;
}

function getCookie($key) {
    $value = '';
    if(isset($_COOKIE[$key])) {
        $value = $_COOKIE[$key];
        $value = fixSqlInject($value);
    }
    return $value;
}
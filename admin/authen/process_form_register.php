<!-- File này giải quyết logic cho register.php -->

<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$fullname = $email = $msg = '';

if (!empty($_POST)) {
    $fullname = getPost('fullname');
    $email = getPost('email');
    $pwd = getPost('password');

    //validate
    if (empty($fullname) || empty($email) || empty($pwd) || strlen($pwd) < 6) {
        # code...






    } else {
        //Validate thành công
        $userExist = executeResult("select * from User where email = '$email'", isSingle: true);
        if ($userExist != null) {
            $msg = 'Email đã được sử dụng!';
        } else {
            $created_at = $updated_at = date('Y-m-d H:i:s');
            //Sử dụng mã hóa 1 chiều -> md5 -> dễ bị hack 
            // -> solution: custom md5
            $pwd = getSecurityMD5($pwd);

            $sql = "insert into User (fullname, email, password, role_id, created_at, updated_at, deleted) values ('$fullname', '$email', '$pwd', 2, '$created_at', '$updated_at', 0)";
            execute($sql);
            header('Location: login.php');
            die();
        }
    }
}
?>
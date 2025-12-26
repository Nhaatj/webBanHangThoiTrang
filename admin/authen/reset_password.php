<?php
require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');

$token = getGet('token');
$msg = '';
$error = false;

// Kiểm tra Token hợp lệ
if (empty($token)) {
    $msg = 'Đường dẫn không hợp lệ!';
    $error = true;
} else {
    $now = date('Y-m-d H:i:s');
    $sql = "SELECT * FROM User WHERE reset_token = '$token' AND reset_token_exp > '$now' AND deleted = 0";
    $user = executeResult($sql, true);

    if ($user == null) {
        $msg = 'Đường dẫn đặt lại mật khẩu không tồn tại hoặc đã hết hạn!';
        $error = true;
    }
}

// Xử lý đổi mật khẩu
if (!empty($_POST) && !$error) {
    $password = getPost('password');
    $confirm_password = getPost('confirm_password');
    $tokenPost = getPost('token');

    if ($password != $confirm_password) {
        $msg = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Hash password
        $passwordHash = getSecurityMD5($password);
        
        // Cập nhật pass mới và xóa token đi (để link này không dùng lại được nữa)
        $sql = "UPDATE User SET password = '$passwordHash', reset_token = NULL, reset_token_exp = NULL, updated_at = now() WHERE reset_token = '$tokenPost'";
        execute($sql);
        
        echo '<script>alert("Đổi mật khẩu thành công! Vui lòng đăng nhập lại."); window.location.href="login.php";</script>';
        die();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../../assets/photos/logo.jpg" />
</head>
<body style="background-image: url('../../assets/photos/ecommerce2.jpg  '); background-position: center; background-size: cover; background-repeat: no-repeat; height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5" style="margin-top: 100px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
                <h3 class="text-center mb-4">ĐẶT LẠI MẬT KHẨU</h3>

                <?php if ($error) { ?>
                    <div class="alert alert-danger"><?= $msg ?></div>
                    <div class="text-center"><a href="login.php">Về trang đăng nhập</a></div>
                <?php } else { ?>
                    
                    <?php if(!empty($msg)) { echo '<div class="alert alert-danger">'.$msg.'</div>'; } ?>

                    <form method="post">
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <div class="form-group">
                            <label>Mật khẩu mới:</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label>Nhập lại mật khẩu mới:</label>
                            <input type="password" class="form-control" name="confirm_password" required minlength="6">
                        </div>
                        <button class="btn btn-success btn-block font-weight-bold">LƯU MẬT KHẨU MỚI</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
session_start();

require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');
require_once('process_form_login.php');

$user = getUserToken();

if ($user != null) {
	header('Location: ../');
	die();
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Registation Form * Form Tutorial</title>
	<meta charset="utf-8">
	<link rel="icon" type="image/png" href="../../assets/photos/logo.jpg" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body style="background-image: url(../../assets/photos/ecommerce2.jpg); background-size: cover; background-repeat: no-repeat; height: 100vh;">
	<div class="container">
		<div class="panel panel-primary" style="width: 480px; margin: 0px auto; margin-top: 50px; background-color: rgba(255, 255, 255, 0.785); padding: 30px; border-radius: 5px; box-shadow: 1rem 2.4rem 4.8rem rgb(183, 235, 244);">
			<div class="panel-heading">
				<h2 class="text-center">Đăng Nhập</h2>
				<h5 id="msg_error" style="color: red; font-size: 20px" class="text-center"><?= $msg ?></h5>
			</div>
			<div class="panel-body">
				<form method="post" style="font-weight: 700">
					<div class="form-group">
						<label for="email">Email:</label>
						<input required="true" type="email" class="form-control" id="email" name="email" value="<?= $email ?>"
							style="<?= ($msg != '') ? 'border: 1px solid red;' : '' ?>">
					</div>
					<div class="form-group">
						<label for="pwd">Mật Khẩu:</label>
						<input required="true" type="password" class="form-control" id="pwd" name="password" minlength="6"
							style="<?= ($msg != '') ? 'border: 1px solid red;' : '' ?>">
					</div>
					<div style="margin-top: 15px; font-size: 14px; font-weight: normal;">
						<div>Chưa có tài khoản? <a href="register.php" style="text-decoration: underline; text-underline-offset: 2px;  text-decoration-color: #007bff;">Đăng ký tài khoản mới</a></div>
						<div>Quên mật khẩu? <a href="forgot_password.php"style="text-decoration: underline; text-underline-offset: 2px;  text-decoration-color: #007bff;">Khôi phục mật khẩu</a></div>
					</div>
					<button class="btn btn-success" style="margin-top: 16px; background-color: rgba(0, 0, 255, 0.621);  width: 30%; font-weight: bold">Đăng Nhập</button>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			// Khi người dùng gõ vào ô Email hoặc Mật khẩu
			$('#email, #pwd').on('input', function() {
				// Nếu đang có lỗi (msg_error có chữ)
				if ($('#msg_error').text() != '') {
					// Xóa viền đỏ của cả 2 ô
					$('#email').css('border-color', '');
					$('#pwd').css('border-color', '');

					// Xóa dòng thông báo lỗi
					$('#msg_error').text('');
				}
			});
		});
	</script>
</body>

</html>
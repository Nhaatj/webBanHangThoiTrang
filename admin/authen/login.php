<?php
session_start();

require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');
require_once('../../process_form_login.php');

$user = getUserToken();
if($user != null) {
    header('Location: ../');
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Registation Form * Form Tutorial</title>
    <meta charset="utf-8">
	<!-- logo shop -->
    <link rel="icon" type="image/png" href="https://gokisoft.com/uploads/2021/03/s-568-ico-web.jpg" />

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body style="background-image: url(../../assets/photos/ecommerce2.jpg); background-size: cover; background-repeat: no-repeat; height: 100vh;">
	<div class="container">
		<div class="panel panel-primary" style="width: 480px; margin: 0px auto; margin-top: 50px; background-color: rgba(255, 255, 255, 0.585); padding: 30px; border-radius: 5px; box-shadow: 1rem 2.4rem 4.8rem rgb(183, 235, 244);">
			<div class="panel-heading">
				<h2 class="text-center">Đăng Nhập</h2>
				<h5 style="color: red" class="text-center"><?=$msg?></h5>
			</div>
			<div class="panel-body">
				<form method="post" style="font-weight: 700">
					<div class="form-group">
						<label for="email">Email:</label>
						<input required="true" type="email" class="form-control" id="email" name="email" value="<?=$email?>">
					</div>
					<div class="form-group">
						<label for="pwd">Mật Khẩu:</label>
						<input required="true" type="password" class="form-control" id="pwd" name="password" minlength="6">
					</div>
					<p>
						<a href="register.php" style="font-size: 14px; font-weight: normal;text-decoration: underline;text-underline-offset: 2px; text-decoration-color: #007bff;">Đăng ký tài khoản mới</a>
					</p>
					<button class="btn btn-success" style="background-color: rgba(0, 0, 255, 0.621);  width: 30%; font-weight: bold">Đăng Nhập</button>
					<!-- display: block; margin: 0px auto; -->
				</form>
			</div>
		</div>
	</div>
</body>
</html>
<?php
require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');
require_once('process_form_register.php');

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
	<!-- logo shop -->
	<link rel="icon" type="image/png" href="../../assets/photos/logo.jpg" />

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
				<h2 class="text-center">Đăng Ký Tài Khoản</h2>
				<!-- <h5 style="color: red" class="text-center"><?= $msg ?></h5> -->
			</div>
			<div class="panel-body">
				<form method="post" onsubmit="return validateForm();" style="font-weight: 700">
					<div class="form-group">
						<label for="usr">Họ & Tên:</label>
						<input required="true" type="text" class="form-control" id="usr" name="fullname" value="<?= $fullname ?>">
					</div>
					<div class="form-group">
						<label for="email">Email:</label>
						<input required="true" type="email" class="form-control" id="email" name="email" value="<?= $email ?>" style="<?= ($msg != '') ? 'border-color: red;' : '' ?>">
						<p id="email_msg" style="color: red; font-size: 14px; font-style: italic; margin-top: 2px;"><?= $msg ?></p>
					</div>
					<div class="form-group">
						<label for="pwd">Mật Khẩu:</label>
						<input required="true" type="password" class="form-control" id="pwd" name="password" minlength="6">
						<p id="msg_pwd" style="color: red; font-size: 14px; font-style: italic; margin-top: 2px;"></p>
					</div>
					<div class="form-group" style="margin-bottom: 20px;">
						<label for="confirmation_pwd">Xác Minh Mật Khẩu:</label>
						<input required="true" type="password" class="form-control" id="confirmation_pwd">
						<p id="msg_confirmation_pwd" style="color: red; font-size: 14px; font-style: italic; margin-top: 2px;"></p>
					</div>
					<p>
						<a href="login.php" style="font-size: 14px; font-weight: normal;text-decoration: underline;text-underline-offset: 2px; text-decoration-color: #007bff;">Tôi đã có tài khoản</a>
					</p>
					<button class="btn btn-success" style="background-color: rgba(0, 0, 255, 0.621);  width: 30%; font-weight: bold">Đăng Ký</button>
					<!-- display: block; margin: 0px auto; -->
				</form>
			</div>
		</div>
	</div>

	<script type="Text/JavaScript">
		function validateForm() {
    // --- PHẦN KIỂM TRA MẬT KHẨU (Client-side) ---
    $pwd = $('#pwd').val();
    $confilmPwd = $('#confirmation_pwd').val();

    // Reset lại style mật khẩu trước khi kiểm tra
    $('#msg_pwd').text("");
    $('#msg_confirmation_pwd').text("");
    $('#pwd').css('border-color', '');
    $('#confirmation_pwd').css('border-color', '');

    if ($pwd != $confilmPwd) {
        // Hiện thông báo ở cả 2 chỗ
        $('#msg_pwd').text("Mật khẩu không khớp, vui lòng kiểm tra lại!");
        $('#msg_confirmation_pwd').text("Mật khẩu không khớp, vui lòng kiểm tra lại!");
        
        // Làm viền ô nhập đỏ lên
        $('#pwd').css('border-color', 'red');
        $('#confirmation_pwd').css('border-color', 'red');
        
        return false; // Chặn submit
    }
    
    return true; // Cho phép submit
  }

  // Khi người dùng nhập lại email, xóa viền đỏ và thông báo lỗi
  $(document).ready(function() {
      $('#email').on('input', function() {
          $('#email').css('border-color', ''); // Trả về viền mặc định
          $('#email_msg').text(''); // Xóa dòng chữ lỗi
      });
  });
</script>
</body>

</html>
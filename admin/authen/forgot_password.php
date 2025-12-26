<?php
session_start();
require_once('../../utils/utility.php');
require_once('../../database/dbhelper.php');

$user = getUserToken();
if($user != null) {
    header('Location: ../../index.php');
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quên mật khẩu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../../assets/photos/logo.jpg" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    
    <style>
        .msg-box {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            display: none; /* Mặc định ẩn */
        }
        .msg-error {
            border: 1px solid red;
            background-color: #ffe6e6;
            color: red;
        }
        .msg-success {
            border: 1px solid green;
            background-color: #e6ffe6;
            color: green;
        }
    </style>
</head>
<body style="background-image: url('../../assets/photos/ecommerce2.jpg  '); background-position: center; background-size: cover; background-repeat: no-repeat; height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5" style="margin-top: 100px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.2);">
                <h3 class="text-center mb-4">KHÔI PHỤC MẬT KHẨU</h3>
                
                <form id="forgotForm">
                    <div class="form-group">
                        <label>Nhập email tài khoản của bạn:</label>
                        <input type="email" class="form-control" id="email" placeholder="Vd: abc@gmail.com">
                        
                        <div id="msg_box" class="msg-box"></div>
                    </div>
                    
                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-block font-weight-bold">LẤY LẠI MẬT KHẨU</button>
                    
                    <div class="text-center mt-3">
                        <a href="login.php" style="color: #000;">Quay lại đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#forgotForm').submit(function(e) {
            e.preventDefault();
            var email = $('#email').val();
            var msgBox = $('#msg_box');
            var btn = $('#btnSubmit');

            // Reset trạng thái
            msgBox.hide().removeClass('msg-error msg-success');
            btn.prop('disabled', true).text('Đang xử lý...');

            $.post('process_forgot.php', {
                'email': email
            }, function(data) {
                var res = JSON.parse(data);
                
                msgBox.text(res.message).show();
                
                if (res.status == 'success') {
                    msgBox.addClass('msg-success');
                } else {
                    msgBox.addClass('msg-error');
                }
                
                btn.prop('disabled', false).text('LẤY LẠI MẬT KHẨU');
            });
        });
    </script>
</body>
</html>
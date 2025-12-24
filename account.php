<?php
require_once('layouts/header.php');

// Kiểm tra đăng nhập, nếu chưa thì đá về trang login
if ($user == null) {
    // Code cũ bị lỗi: header('Location: admin/authen/login.php'); 
    // Nguyên nhân: Do header.php đã output HTML nên lệnh header() PHP bị lỗi.
    
    // Sửa thành: Dùng Javascript để chuyển hướng
    echo '<script>window.location.href = "index.php";</script>';
    die();
}

// Lấy thông báo nếu có
$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    $msg = "Cập nhật thông tin thành công!";
}
?>

<div class="container" style="margin-bottom: 50px;">
    <ul class="breadcrumb" style="text-decoration: none">
        <li class="breadcrumb-item">
            <a href="index.php" style="text-decoration: none; color:black">Trang Chủ</a>
        </li>
        <li class="breadcrumb-item active">Tài Khoản</li>
    </ul>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="text-center text-uppercase font-weight-bold">Thông tin tài khoản</h3>
            
            <?php if($msg != '') { ?>
                <div class="alert alert-success text-center"><?= $msg ?></div>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="api/user_actions.php" method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Email (Tên đăng nhập):</label>
                            <input type="text" class="form-control" value="<?= $user['email'] ?>" readonly style="background-color: #e9ecef;">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Họ & Tên:</label>
                            <input type="text" class="form-control" name="fullname" value="<?= $user['fullname'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Số điện thoại:</label>
                            <input type="text" class="form-control" name="phone_number" value="<?= $user['phone_number'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Địa chỉ:</label>
                            <input type="text" class="form-control" name="address" value="<?= $user['address'] ?>">
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div class="mt-4">
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#changePassModal">
                                    <i class="fa fa-key"></i> Đổi mật khẩu
                                </button>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-dark" style="width: 150px;">Lưu thay đổi</button>
                                <a href="api/user_actions.php?action=logout" class="btn btn-secondary" style="width: 150px;">Đăng xuất</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="top: 23%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Đổi Mật Khẩu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formChangePass">
            <div class="form-group">
                <label>Mật khẩu cũ:</label>
                <input type="password" class="form-control" name="old_pass" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu mới:</label>
                <input type="password" class="form-control" name="new_pass" id="new_pass" required minlength="6">
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu mới:</label>
                <input type="password" class="form-control" name="confirm_pass" id="confirm_pass" required minlength="6">
            </div>
            <p id="msg_changepass" class="text-danger"></p>
            <button type="submit" class="btn btn-primary btn-block">Cập nhật</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    $('#formChangePass').submit(function(e) {
        e.preventDefault();
        
        var old_pass = $('[name=old_pass]').val();
        var new_pass = $('[name=new_pass]').val();
        var confirm_pass = $('[name=confirm_pass]').val();

        if (new_pass != confirm_pass) {
            $('#msg_changepass').text('Mật khẩu xác nhận không khớp!');
            return;
        }

        $.post('api/user_actions.php', {
            'action': 'change_password',
            'old_pass': old_pass,
            'new_pass': new_pass
        }, function(data) {
            if (data == 'success') {
                alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
                location.href = 'api/user_actions.php?action=logout';
            } else {
                $('#msg_changepass').text(data);
            }
        })
    });
</script>

<?php
require_once('layouts/footer.php');
?>
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

                        <div class="text-center mt-4">
                            <a href="api/user_actions.php?action=logout" class="btn btn-secondary" style="width: 150px;">Đăng xuất</a>
                            
                            <button type="submit" class="btn btn-dark" style="width: 150px;">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once('layouts/footer.php');
?>
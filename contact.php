<?php
require_once('layouts/header.php');

if(!empty($_POST)) {
    $fullname = getPost('fullname');
    $email = getPost('email');
    $phone_number = getPost('phone_number');
    $subject_name = getPost('subject_name');
    $note = getPost('note');
    $created_at =  $updated_at = date('Y-m-d H:i:s');

    $sql = "insert into Feedback(fullname, email, phone_number, subject_name, note, created_at, updated_at, status) values('$fullname', '$email', '$phone_number', '$subject_name', '$note', '$created_at', '$updated_at', 0)";
    execute($sql);
}

// Kiểm tra đăng nhập
$isLoggedIn = isset($user) && $user != null;
$fullname = $isLoggedIn ? $user['fullname'] : '';
$email = $isLoggedIn ? $user['email'] : '';
$phone_number = $isLoggedIn ? $user['phone_number'] : '';

?>

<style>
    .section-title {
        display: flex;
        justify-content: center;
    }

    .section-title h4 {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 22px;
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 2px solid #000;
        display: inline-block;
    }

    .btn-submit {
        background-color: #000;
        color: #fff;
        font-weight: bold;
        border: 1px solid #000;
    }
    .btn-submit:hover {
        background-color: #fff;
        color: #000;
    }
    .btn-submit:disabled {
        background-color: #ccc;
        border-color: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="container" style="margin-bottom: 50px;">
    <ul class="breadcrumb" style="background: transparent; padding-left: 0;">
        <li class="breadcrumb-item"><a href="index.php" style="color: #333; text-decoration: none;">Trang Chủ</a></li>
        <li class="breadcrumb-item active">Liên hệ</li>
    </ul>

    <form method="POST" style="font-weight: 700;">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h4>Thông tin liên hệ</h4>
                </div>
                <div class="form-group">
                    <label for="fullname">Họ và tên:</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="" value="<?=$fullname?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="phone_number">SĐT:</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="" value="<?=$phone_number?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="" value="<?=$email?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject_name">Chủ đề:</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="" value="" required>
                </div>
                <div class="form-group">
                    <label for="note">Nội dung:</label>
                    <textarea class="form-control" id="note" name="note" rows="5" placeholder="" maxlength="750"></textarea>
                </div>
                <button type="submit" class="btn btn-submit w-100 py-3 text-uppercase">GỬI PHẢN HỒI</button>
            </div>
        </div>
    </form>
</div>

<?php
require_once('layouts/footer.php');
?>
<?php
$title = 'Thêm/Sửa Tài Khoản Người Dùng';
$baseUrl = '../';
$titleHeader = 'Thêm/Sửa Tài Khoản Người Dùng';
require_once('../layouts/header.php');

$id = $msg = $fullname = $email = $phone_number = $address = $role_id = '';
require_once('form_save.php');

$id = getGet('id');
if ($id != '' && $id > 0) {
  $sql = "select * from User where id = '$id'";
  $userItem = executeResult($sql, true);
  if ($userItem != null) {
    $fullname = $userItem['fullname'];
    $email = $userItem['email'];
    $phone_number = $userItem['phone_number'];
    $address = $userItem['address'];
    $role_id = $userItem['role_id'];
  } else {
    $id = 0;
  }
} else {
  $id = 0;
}

$sql = "select * from Role";
$roleItems = executeResult($sql);
?>

<div class="row" style="margin-top: 20px;">
  <div class="col-md-12 table-responsive">
    <!-- <h3>Thêm/Sửa Tài Khoản Người Dùng</h3> -->

    <div class="panel panel-primary">
      <div class="panel-heading">
        <!-- <h5 style="color: red;"><?= $msg ?></h5> -->
      </div>
      <div class="panel-body">
        <form method="post" onsubmit="return validateForm();" style="font-weight: 700">
          <div class="form-group">
            <label for="usr">Họ & Tên:</label>
            <input required="true" type="text" class="form-control" id="usr" name="fullname" value="<?= $fullname ?>">
            <input type="text" name="id" value="<?= $id ?>" hidden="true">
          </div>
          <div class="form-group">
            <label for="role_id">Role:</label>
            <select class="form-control" name="role_id" id="role_id" required="true">
              <option value="">-- Chọn --</option>
              <?php
              foreach ($roleItems as $role) {
                if ($role['id'] == $role_id) {
                  echo '<option selected value="' . $role['id'] . '">' . $role['name'] . '</option>';
                } else {
                  echo '<option value="' . $role['id'] . '">' . $role['name'] . '</option>';
                }
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="email">Email:</label>
            <input <?= ($id == 0) ? 'required="true"' : 'disabled' ?> type="email" class="form-control" id="email" name="email" value="<?= $email ?>" style="<?= ($msg != '') ? 'border-color: red;' : '' ?>">
            <p id="email_msg" style="color: red; font-size: 14px; font-style: italic; margin-top: 2px;"><?= $msg ?></p>
          </div>
          <div class="form-group">
            <label for="phone_number">SĐT:</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?= $phone_number ?>">
          </div>
          <div class="form-group">
            <label for="address">Địa Chỉ:</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= $address ?>">
          </div>
          <?php if($id == 0) { ?>
            <div class="form-group">
              <label for="pwd">Mật Khẩu:</label>
              <input required="true" type="password" class="form-control" id="pwd" name="password" minlength="6">
              <p id="msg_pwd" style="color: red; font-size: 14px; font-style: italic; margin-top: 2px;"></p>
            </div>
            <div class="form-group">
              <label for="confirmation_pwd">Xác Minh Mật Khẩu:</label>
              <input required="true" type="password" class="form-control" id="confirmation_pwd">
              <p id="msg_confirmation_pwd" style="color: red; font-size: 14px; font-style: italic; margin-top: 2px;"></p>
            </div>
          <?php } ?>
          
          <button class="btn btn-success" style="background-color: rgba(0, 0, 255, 0.621);  width: 20%; font-weight: bold; margin-top: 14px;"">Lưu</button>
        </form>
      </div>
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

<?php
require_once('../layouts/footer.php');
?>
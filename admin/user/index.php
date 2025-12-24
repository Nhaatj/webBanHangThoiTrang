<?php
$title = 'Quản Lý Người Dùng';
$baseUrl = '../';
$titleHeader = 'Quản Lý Người Dùng';
require_once('../layouts/header.php');

$sql = "select User.*, Role.name as role_name from User left join Role on User.role_id = Role.id where User.deleted = 0";
$data = executeResult($sql);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12 table-responsive">
        <div>
            <!-- <h3 style="margin-bottom: 0;">Quản Lý Người Dùng</h3> -->
            <a href="editor.php"><button class="btn btn-success">Thêm Tài Khoản</button></a>
        </div>
            
        <table class="table table-bordered table-hover" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ & Tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Địa Chỉ</th>
                    <th>Quyền</th>
                    <th style="width: 50px"></th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;
                foreach ($data as $item) {
                    echo '<tr>
                            <td>' . (++$index) . '</td>
                            <td>' . $item['fullname'] . '</td>
                            <td>' . $item['email'] . '</td>
                            <td>' . $item['phone_number'] . '</td>
                            <td>' . $item['address'] . '</td>
                            <td>' . $item['role_name'] . '</td>
                            <td style="width: 50px">
                                <a href="editor.php?id=' . $item['id'] . '"><button class="btn btn-warning">Sửa</button></a>
                            </td>
                            <td style="width: 50px">';
                    // Nếu ID tk đang đăng nhập != ID tk của item trong vòng lặp hiện tại --> Hiện nút 'Xóa'
                    // Và ngược lại --> Không có nút 'Xóa'
                    // if ($user['id'] != $item['id'] && $item['role_id'] != 1)
                    if ($item['role_id'] != 1) {
                        echo '<button onclick="deleteUser(' . $item['id'] . ')" class="btn btn-danger">Xóa</button>';
                    }
                    echo '        
                            </td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function deleteUser(id) {
        option = confirm('Bạn có chắc muốn xóa tài khoản này không?')
        if (!option) return;

        $.post(
            'form_api.php', 
            {
                'id': id,
                'action': 'delete'
            },
            function(data) {
                location.reload()
            }
        )
    }
</script>

<?php
require_once('../layouts/footer.php');
?>
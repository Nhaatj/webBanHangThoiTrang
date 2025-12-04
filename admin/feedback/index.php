<?php
$title = 'Quản Lý Phản Hồi';
$baseUrl = '../';
require_once('../layouts/header.php');

// Lấy danh sách phản hồi chưa bị xóa (status != 3)
// status = 0: Chưa đọc (màu trắng)
// status = 1: Đã đọc (màu xám)
// status = 2: Đã xóa (ẩn)
$sql = "select * from FeedBack where status != 2 order by status asc, updated_at asc";
$data = executeResult($sql);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <h3>Quản Lý Phản Hồi</h3>
    </div>
    <div class="col-md-12" style="margin-top: 10px; margin-bottom: 10px;">
        <button class="btn btn-warning" onclick="markRead()">Đã Đọc</button>
        <button class="btn btn-danger" onclick="deleteFeedback()">Xóa</button>
    </div>

    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên</th>
                    <th>Họ</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Chủ Đề</th>
                    <th>Nội Dung</th>
                    <th>Ngày Tạo</th>
                    <th style="width: 50px; text-align: center;">
                        <input type="checkbox" id="checkAll" onchange="toggleAll(this)">
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;
                foreach ($data as $item) {
                    // Nếu status = 1 (đã đọc) thì cho nền màu xám
                    $rowStyle = ($item['status'] == 1) ? 'style="background-color: #e9ecef; color: #6c757d;"' : '';

                    echo '<tr id="tr_' . $item['id'] . '" ' . $rowStyle . '>
                            <td>' . (++$index) . '</td>
                            <td>' . $item['firstname'] . '</td>
                            <td>' . $item['lastname'] . '</td>
                            <td>' . $item['phone_number'] . '</td>
                            <td>' . $item['email'] . '</td>
                            <td>' . $item['subject_name'] . '</td>
                            <td>' . $item['note'] . '</td>
                            <td>' . $item['updated_at'] . '</td>
                            <td style="width: 50px; text-align: center;">
                                <input type="checkbox" class="feedback-checkbox" value="' . $item['id'] . '">
                            </td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    // Hàm chọn/bỏ chọn tất cả
    function toggleAll(source) {
        checkboxes = document.getElementsByClassName('feedback-checkbox');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    // Hàm lấy danh sách ID đã tick
    function getSelectedIds() {
        var ids = [];
        $('.feedback-checkbox:checked').each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    // Xử lý nút "Đã đọc"
    function markRead() {
        // Fix lỗi button bị giữ focus sau khi click
        if (document.activeElement) {
            document.activeElement.blur();
        }

        var ids = getSelectedIds();
        if (ids.length == 0) {
            alert('Vui lòng chọn ít nhất một phản hồi!');
            return;
        }

        $.post(
            'form_api.php', {
                'ids': JSON.stringify(ids),
                'action': 'mark_read'
            },
            function(data) {
                // Xử lý giao diện sau khi thành công
                for (var i = 0; i < ids.length; i++) {
                    // 1. Chuyển màu xám cho dòng
                    $('#tr_' + ids[i]).css('background-color', '#e9ecef').css('color', '#6c757d');
                    // 2. Reset (bỏ tick) checkbox
                    $('input[value="' + ids[i] + '"]').prop('checked', false);
                }
                // Bỏ tick nút "Chọn tất cả" nếu có
                $('#checkAll').prop('checked', false);
            }
        );
    }

    // Xử lý nút "Xóa"
    function deleteFeedback() {
        // Fix lỗi button bị giữ focus sau khi click
        if (document.activeElement) {
            document.activeElement.blur();
        }

        var ids = getSelectedIds();
        if (ids.length == 0) {
            alert('Vui lòng chọn ít nhất một phản hồi để xóa!');
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn xóa các phản hồi đã chọn không?')) return;

        $.post(
            'form_api.php', {
                'ids': JSON.stringify(ids),
                'action': 'delete'
            },
            function(data) {
                // Xử lý giao diện: Xóa hàng khỏi bảng
                for (var i = 0; i < ids.length; i++) {
                    $('#tr_' + ids[i]).remove();
                }
                // Bỏ tick nút "Chọn tất cả"
                $('#checkAll').prop('checked', false);
            }
        );
    }
</script>

<?php
require_once('../layouts/footer.php');
?>
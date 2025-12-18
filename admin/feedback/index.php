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
    <div class="col-md-12" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px">
        <h3 style="margin-bottom: 0;">Quản Lý Phản Hồi</h3>
        <div>
            <button class="btn btn-warning" onclick="markRead()">Đã Đọc</button>
            <button class="btn btn-danger" onclick="deleteFeedback()">Xóa</button>
        </div>
    </div>

    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ & Tên</th>
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
                            <td>' . $item['fullname'] . '</td>
                            <td>' . $item['phone_number'] . '</td>
                            <td>' . $item['email'] . '</td>
                            <td>' . $item['subject_name'] . '</td>
                            <td>' . $item['note'] . '</td>
                            <td>' . $item['updated_at'] . '</td>
                            <td style="width: 50px; text-align: center;">
                                <input type="checkbox" class="feedback-checkbox" value="' . $item['id'] . '" data-status="' . $item['status'] . '">
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
        // ids là mảng chứa id của các feedback đã check
        var ids = [];
        $('.feedback-checkbox:checked').each(function() {
            ids.push($(this).val());
            // hàm val lấy giá trị của thuộc tính value của thẻ checkbox đã check (Tức thẻ chứa class feedback-checkbox đã check)
        });
        return ids;
    }

    // Xử lý nút "Đã đọc"
    function markRead() {
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
                for (var i = 0; i < ids.length; i++) {
                    $('#tr_' + ids[i]).css('background-color', '#e9ecef').css('color', '#6c757d');
                    var checkbox = $('input[value="' + ids[i] + '"]');
                    checkbox.prop('checked', false);

                    // QUAN TRỌNG: Cập nhật lại status thành 1 để sau này có thể xóa được ngay
                    checkbox.attr('data-status', 1);
                }
                $('#checkAll').prop('checked', false);
            }
        );
    }

    // Xử lý nút "Xóa"
    function deleteFeedback() {
        if (document.activeElement) {
            document.activeElement.blur();
        }

        // Logic lọc ID: Chỉ lấy những ID nào có status != 0
        var ids = [];
        var hasUnread = false; // Cờ kiểm tra có tin chưa đọc không

        $('.feedback-checkbox:checked').each(function() {
            var status = $(this).attr('data-status');
            if (status == 0) {
                hasUnread = true; // Phát hiện tin chưa đọc
            } else {
                ids.push($(this).val()); // Chỉ thêm tin đã đọc vào danh sách xóa
            }
        });

        if (hasUnread) {
            alert('Bạn không được xóa những phản hồi "Chưa Đọc"!');
        }

        if (ids.length == 0) {
            if (!hasUnread) {
                alert('Vui lòng chọn ít nhất một phản hồi để xóa!');
            }
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn xóa ' + ids.length + ' phản hồi ĐÃ ĐỌC được chọn không?')) return;

        $.post(
            'form_api.php', {
                'ids': JSON.stringify(ids),
                'action': 'delete'
            },
            function(data) {
                for (var i = 0; i < ids.length; i++) {
                    $('#tr_' + ids[i]).remove();
                }
                $('#checkAll').prop('checked', false);
            }
        );
    }
</script>

<?php
require_once('../layouts/footer.php');
?>
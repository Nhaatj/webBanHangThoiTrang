<?php
$title = 'Quản Lý Đơn Hàng';
$baseUrl = '../';
require_once('../layouts/header.php');


// status = 0: pending 
// status = 1: approved 
// status = 2: cancel
$sql = "select * from Orders order by status asc, order_date desc";
$data = executeResult($sql);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12 table-responsive">
        <h3 style="margin-bottom: 10px;">Quản Lý Đơn Hàng</h3>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ & Tên</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Địa Chỉ</th>
                    <th>Nội dung</th>
                    <th>Tổng Tiền</th>
                    <th>Ngày Tạo</th>
                    <th style="width: 120px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;
                foreach ($data as $item) {
                    echo '<tr style="cursor: pointer;" onclick="window.location=\'detail.php?id=' . $item['id'] . '\'">
                            <td>' . (++$index) . '</td>
                            <td>' . $item['fullname'] . '</td>
                            <td>' . $item['phone_number'] . '</td>
                            <td>' . $item['email'] . '</td>
                            <td>' . $item['address'] . '</td>
                            <td>' . $item['note'] . '</td>
                            <td>' . $item['total_money'] . '</td>
                            <td>' . $item['order_date'] . '</td>
                            <td style="width: 50px; cursor: default" onclick="event.stopPropagation()">';
                                if ($item['status'] == 0) {
                                    echo '<button onclick="changeStatus(' . $item['id'] . ', 1)" class="btn btn-success" style="margin-bottom: 10px">Approve</button>
                                    <button onclick="changeStatus(' . $item['id'] . ', 2)" class="btn btn-danger" style="width: 86.11px">Cancel</button>';
                                } else if ($item['status'] == 1) {
                                    echo '<label class="badge badge-success">Approved</label>';
                                } else {
                                    echo '<label class="badge badge-danger">Canceled</label>';
                                }
                      echo '</td>
                          </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function changeStatus(id, status) {
        $.post(
            'form_api.php', {
                'id': id,
                'status': status,
                'action': 'update_status'
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
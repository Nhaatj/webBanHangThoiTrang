<?php
require_once('utils/utility.php');
require_once('database/dbhelper.php');
require_once('layouts/header.php');
require_once('config_vnpay.php');

$msg = "";
$orderId = "";

// ------------------------------------------
// 1. XỬ LÝ KẾT QUẢ VNPAY TRẢ VỀ
// ------------------------------------------
if (isset($_GET['vnp_SecureHash'])) {
    $vnp_SecureHash = $_GET['vnp_SecureHash'];
    $inputData = array();
    foreach ($_GET as $key => $value) {
        if (substr($key, 0, 4) == "vnp_") {
            $inputData[$key] = $value;
        }
    }
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $i = 0;
    $hashData = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }
    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    $orderId = $_GET['vnp_TxnRef'];

    if ($secureHash == $vnp_SecureHash) {
        if ($_GET['vnp_ResponseCode'] == '00') {
            // THANH TOÁN THÀNH CÔNG -> Update status = 1 (Đã thanh toán)
            execute("UPDATE Orders SET status = 1 WHERE id = $orderId");
            $msg = "Thanh toán VNPAY thành công!";
        } else {
            execute("UPDATE Orders SET status = 2 WHERE id = $orderId");
            $msg = "Giao dịch VNPAY thất bại hoặc bị hủy.";
        }
    } else {
        $msg = "Chữ ký VNPAY không hợp lệ.";
    }
}

// --- XỬ LÝ KẾT QUẢ MOMO ---
elseif (isset($_GET['partnerCode']) && isset($_GET['resultCode'])) {
    $resultCode = $_GET['resultCode'];
    // Tách lấy ID đơn hàng gốc (Do lúc gửi đi mình gửi dạng ID_Time)
    $rawOrderId = $_GET['orderId'];
    $parts = explode("_", $rawOrderId); 
    $orderId = $parts[0];

    if ($resultCode == '0') {
        execute("UPDATE Orders SET status = 1 WHERE id = $orderId");
        $msg = "Thanh toán MoMo thành công!";
    } else {
        execute("UPDATE Orders SET status = 2 WHERE id = $orderId");
        $msg = "Thanh toán MoMo thất bại (Mã lỗi: " . $resultCode . ")";
    }
}

// ------------------------------------------
// 3. XỬ LÝ COD (TRUYỀN THẲNG ID)
// ------------------------------------------
elseif (isset($_GET['order_id'])) {
    $orderId = getGet('order_id');
    $msg = "Đặt hàng thành công! (Thanh toán khi nhận hàng)";
}

// ------------------------------------------
// 4. LẤY THÔNG TIN ĐƠN HÀNG ĐỂ HIỂN THỊ
// ------------------------------------------
$orderItem = null;
$total_detail = 0;
if ($orderId != "") {
    $sql = "SELECT * FROM Orders WHERE id = $orderId";
    $orderItem = executeResult($sql, true);
    
    // Lấy chi tiết sản phẩm
    $sqlDetails = "SELECT Order_Details.*, Product.title, Product.thumbnail 
                   FROM Order_Details 
                   LEFT JOIN Product ON Order_Details.product_id = Product.id 
                   WHERE Order_Details.order_id = $orderId";
    $orderDetails = executeResult($sqlDetails);

    foreach ($orderDetails as $item) {
        // Tổng tiền sản phẩm trong order details (Không bao gồm phí ship)
        $total_detail += $item['total_money'];
    }
}
?>

<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h4>KẾT QUẢ ĐẶT HÀNG</h4>
                </div>
                <div class="card-body">
                    <?php if($msg != ""): ?>
                        <div class="alert alert-info text-center font-weight-bold">
                            <?= $msg ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($orderItem != null): ?>
                        <h5 class="text-uppercase border-bottom pb-2">Thông tin đơn hàng #<?= $orderItem['id'] ?></h5>
                        <p><strong>Họ tên:</strong> <?= $orderItem['fullname'] ?></p>
                        <p><strong>SĐT:</strong> <?= $orderItem['phone_number'] ?></p>
                        <p><strong>Địa chỉ:</strong> <?= $orderItem['address'] ?></p>
                        <p><strong>Ngày đặt:</strong> <?= $orderItem['order_date'] ?></p>
                        <p><strong>Trạng thái:</strong> 
                            <?php 
                                if($orderItem['status'] == 0) echo '<span class="badge badge-warning">Chờ thanh toán / COD</span>';
                                else if($orderItem['status'] == 1) echo '<span class="badge badge-success">Đã thanh toán</span>';
                                else echo '<span class="badge badge-danger">Đã hủy</span>';
                            ?>
                        </p>

                        <h5 class="mt-4 border-bottom pb-2">Chi tiết sản phẩm</h5>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Hình</th>
                                    <th>Sản phẩm</th>
                                    <th>Size</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderDetails as $item): ?>
                                <tr>
                                    <td><img src="<?= fixUrl($item['thumbnail'], '') ?>" width="50px"></td>
                                    <td><?= $item['title'] ?></td>
                                    <td><?= $item['size'] ?></td>
                                    <td class="text-right"><?= $item['num'] ?></td>
                                    <td class="text-right"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                                    <td class="text-right"><?= number_format($item['total_money'], 0, ',', '.') ?>₫</td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold">
                                    <?php if ($orderItem['address'] == "Nhận tại cửa hàng M&N" || $total_detail >= 299000) {
                                        echo '<td colspan="5" class="text-left">TỔNG CỘNG:</td>';
                                    } else {
                                        echo '<td colspan="5" class="text-left">TỔNG CỘNG (Bao gồm phí vận chuyển):</td>';
                                    }?>
                                    <td class="text-danger text-right"><?= number_format($orderItem['total_money'], 0, ',', '.') ?>₫</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p>Không tìm thấy thông tin đơn hàng.</p>
                            <a href="index.php" class="btn btn-dark">Về trang chủ</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once('layouts/footer.php');
?>

<?php
require_once('layouts/header.php');

$orderId = getGet('id');
if ($user == null || $orderId == '') {
    echo '<script>window.location.href = "index.php";</script>';
    die();
}

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM Orders WHERE id = $orderId AND user_id = " . $user['id'];
$order = executeResult($sql, true);

if ($order == null) {
    echo '<div class="container pt-5"><h4>Không tìm thấy đơn hàng!</h4></div>';
    require_once('layouts/footer.php');
    die();
}

// Lấy chi tiết sản phẩm
$sqlDetails = "SELECT Order_Details.*, Product.title, Product.thumbnail, Product.price as original_price 
               FROM Order_Details 
               JOIN Product ON Order_Details.product_id = Product.id 
               WHERE order_id = $orderId";
$items = executeResult($sqlDetails);

// Logic hiển thị trạng thái và màu sắc
$statusLabel = "";
$statusColor = "";
switch($order['status']) {
    case 0: $statusLabel = "Chờ xử lý thanh toán"; $statusColor = "text-warning"; break;
    case 1: $statusLabel = "Đã xác nhận thanh toán / Chờ nhận hàng"; $statusColor = "text-primary"; break;
    case 2: $statusLabel = "Thành công"; $statusColor = "text-success"; break;
    case 3: $statusLabel = "Đã hủy"; $statusColor = "text-danger"; break;
}

// Logic ngày nhận
$receivedDate = "Chưa nhận hàng";
if ($order['status'] == 2) {
    // Nếu có cột received_date và khác null
    if(!empty($order['received_date'])) {
        $receivedDate = date('H:i d/m/Y', strtotime($order['received_date']));
    } else {
        $receivedDate = "Đã nhận hàng"; 
    }
}

// Logic hình thức thanh toán
$paymentMethodText = "Thanh toán tiền mặt (COD)";
// Kiểm tra xem trong $order có cột payment_method không
if (isset($order['payment_method']) && $order['payment_method'] != '') {
    if ($order['payment_method'] == 'COD') {
        $paymentMethodText = "Thanh toán khi nhận hàng (COD)";
    } elseif ($order['payment_method'] == 'VNPAY') {
        $paymentMethodText = "Thanh toán qua VNPAY";
    } elseif ($order['payment_method'] == 'MOMO') {
        $paymentMethodText = "Thanh toán qua Ví MoMo";
    }
}
$isCanceled = ($order['status'] == 3) ? true : false;

// Không hiện nếu đơn hàng đã bị hủy
$paymentMethod_html = '<div class="detail-section">
                            <div class="section-head"><i class="fa-regular fa-credit-card"></i>&nbsp;&nbsp;Hình thức thanh toán</div>
                            <div style="font-size: 14px; font-weight: 600">'. $paymentMethodText .'</div>
                        </div>';
if($isCanceled) {
    $paymentMethod_html = '';
}
?>

<style>
    .detail-section { background: #fff; padding: 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .section-head { text-transform: uppercase; font-weight: bold; font-size: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: #333; }
    
    .info-line { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
    .info-label { color: #777; }
    .info-value { font-weight: 500; text-align: right; }

    .item-row { display: flex; border-bottom: 1px solid #f5f5f5; padding: 15px 0; }
    .item-row:last-child { border-bottom: none; }
    .item-thumb { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; margin-right: 15px; }
    .item-info { flex: 1; }
    .item-price-box { text-align: right; }
    .price-final { font-weight: bold; color: #333; font-size: 15px; }
    .price-origin { font-size: 12px; color: #999; text-decoration: line-through; display: block; margin-top: 2px; }

    .summary-box { border-top: 1px dashed #ddd; padding-top: 15px; margin-top: 10px; }
    .sum-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
    .final-row { display: flex; justify-content: space-between; margin-top: 10px; font-size: 16px; font-weight: bold; color: #d0011b; }
    .btn-checkout {
        background-color: #000;
        color: #fff;
        font-weight: bold;
        border: 1px solid #000;
    }
    .btn-checkout:hover {
        background-color: #fff;
        color: #000;
    }
</style>

<div class="container" style="margin-top: 20px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Chi tiết đơn hàng #<?= $order['id'] ?> - <span class="<?= $statusColor ?>"><?= $statusLabel ?></span></h5>
        <span class="text-muted small"><i class="fa-regular fa-clock"></i>&nbsp;&nbsp;Đặt lúc: <?= date('H:i, d/m/Y', strtotime($order['order_date'])) ?></span>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="detail-section">
                <div class="section-head"><i class="fa-regular fa-address-card"></i>&nbsp;&nbsp;Thông tin nhận hàng</div>
                <div class="info-line">
                    <span class="info-label">Người nhận:</span>
                    <span class="info-value"><?= $order['fullname'] ?></span>
                </div>
                <div class="info-line">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value"><?= $order['phone_number'] ?></span>
                </div>
                <div class="info-line">
                    <span class="info-label">Nhận tại:</span>
                    <span class="info-value"><?= $order['address'] ?></span>
                </div>
                <div class="info-line">
                    <span class="info-label">Nhận lúc:</span>
                    <span class="info-value"><?= $receivedDate ?></span>
                </div>
            </div>

            <?=$paymentMethod_html?>

            <a href="order_history.php" class="text-primary font-weight-bold text-decoration-none">
                <button type="submit" class="btn btn-checkout w-100 py-3 text-uppercase">
                    <i class="fas fa-arrow-left mr-1"></i> VỀ TRANG DANH SÁCH ĐƠN HÀNG
                </button>
            </a>
        </div>

        <div class="col-md-8">
            <div class="detail-section">
                <div class="section-head"><i class="fa-solid fa-bag-shopping"></i>&nbsp;&nbsp;Thông tin sản phẩm</div>
                
                <?php 
                $tempTotalOriginal = 0;
                $tempTotalDiscount = 0;
                
                foreach($items as $item): 
                    $rowTotalDiscount = $item['price'] * $item['num'];
                    $rowTotalOriginal = $item['original_price'] * $item['num'];
                    
                    $tempTotalDiscount += $rowTotalDiscount;
                    $tempTotalOriginal += $rowTotalOriginal;

                    $formatted_price = number_format($rowTotalDiscount, 0, ',', '.');
                    
                    // 2. Xử lý Giá cũ (Chỉ hiện nếu có giảm giá)
                    $old_price_html = '';
                    if($item['original_price'] > $item['price']) {
                        $old_price_html = '<span class="price-origin"><del>' . number_format($rowTotalOriginal, 0, ',', '.') . '<sup><u>đ</u></sup></del></span>';
                    }
                ?>
                <div class="item-row">
                    <img src="<?= fixUrl($item['thumbnail'], '') ?>" class="item-thumb">
                    <div class="item-info">
                        <div class="font-weight-bold mb-1"><?= $item['title'] ?></div>
                        <?php if ($item['size'] != NULL) { ?>
                            <div class="small text-muted">Size: <?= $item['size'] ?></div>
                        <?php } ?>
                        <div class="small text-muted">Số lượng: <?= $item['num'] ?></div>
                    </div>
                    <div class="item-price-box">
                        <span class="price-final"><?= number_format($rowTotalDiscount, 0, ',', '.') ?>₫</span>
                        <?=$old_price_html?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php 
                    // Tính ngược phí ship = Tổng đơn - Tổng tiền hàng
                    $shippingFee = $order['total_money'] - $tempTotalDiscount;
                    if($shippingFee < 0) $shippingFee = 0;
                ?>

                <div class="summary-box">
                    <div class="sum-row">
                        <span class="text-muted">Tạm tính:</span>
                        <span><?= number_format($tempTotalOriginal, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="sum-row">
                        <span class="text-muted">Tổng tiền:</span>
                        <span><?= number_format($tempTotalDiscount, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="sum-row">
                        <span class="text-muted">Phí giao hàng:</span>
                        <span><?= number_format($shippingFee, 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="final-row">
                        <span><?= ($order['status'] == 1 || $order['status'] == 2) ? 'Số tiền đã thanh toán:' : 'Cần thanh toán:' ?></span>
                        <span><?= number_format($order['total_money'], 0, ',', '.') ?>₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('layouts/footer.php'); ?>
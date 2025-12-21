<?php
require_once('layouts/header.php');

if ($user == null) {
    echo '<script>window.location.href = "index.php";</script>';
    die();
}

$userId = $user['id'];
$status = getGet('status'); // Lấy status từ URL để lọc

// Tạo query lọc theo status
$sql_status = "";
if ($status != '' && $status != 'all') {
    $sql_status = " AND status = $status";
}

$sql = "SELECT * FROM Orders WHERE user_id = $userId $sql_status ORDER BY order_date DESC";
$orders = executeResult($sql);

// Hàm hiển thị trạng thái text
function getStatusText($status) {
    switch ($status) {
        case 0: return '<span class="text-warning font-weight-bold"><i class="fas fa-spinner"></i> Chờ xử lý/thanh toán</span>';
        case 1: return '<span class="text-primary font-weight-bold"><i class="fas fa-truck"></i> Đã thanh toán, chờ nhận hàng</span>';
        case 2: return '<span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Đã nhận hàng</span>';
        case 3: return '<span class="text-danger font-weight-bold"><i class="fas fa-times-circle"></i> Đã hủy</span>';
        default: return 'Không xác định';
    }
}
?>

<style>
    /* CSS giống TGDD */
    .order-filter { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 20px; overflow-x: auto; }
    .order-filter a { padding: 10px 20px; color: #333; text-decoration: none; white-space: nowrap; font-weight: 500; }
    .order-filter a.active { border-bottom: 2px solid #2f80ed; color: #2f80ed; }
    
    .order-item { background: #fff; border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 15px; padding: 15px; }
    .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f1f1; padding-bottom: 10px; margin-bottom: 10px; font-size: 14px; color: #666; }
    .order-body { display: flex; align-items: center; justify-content: space-between; }
    
    /* Hiển thị list sản phẩm nhỏ trong đơn hàng */
    .product-preview { display: flex; align-items: center; gap: 10px; }
    .product-preview img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
    .total-price { font-weight: bold; color: #d0021b; font-size: 16px; }
    
    .btn-detail { border: 1px solid #2f80ed; color: #2f80ed; padding: 5px 15px; border-radius: 4px; text-decoration: none; font-size: 14px; transition: 0.2s; }
    .btn-detail:hover { background: #2f80ed; color: #fff; }
    .ordered-at::before {
        content: " ";
        display: inline-block;
        width: 4px;
        height: 4px;
        background-color: #000;
        border-radius: 50%;
        margin: 3px 10px;
    }
</style>

<div class="container" style="margin-top: 20px; margin-bottom: 50px; min-height: 500px;">
    <h4 class="text-uppercase mb-3">Lịch sử mua hàng</h4>

    <div class="order-filter">
        <a href="order_history.php" class="<?= ($status=='')?'active':'' ?>">Tất cả</a>
        <a href="order_history.php?status=0" class="<?= ($status=='0')?'active':'' ?>">Chờ xử lý</a>
        <a href="order_history.php?status=1" class="<?= ($status=='1')?'active':'' ?>">Đã xác nhận</a>
        <a href="order_history.php?status=2" class="<?= ($status=='2')?'active':'' ?>">Thành công</a>
        <a href="order_history.php?status=3" class="<?= ($status=='3')?'active':'' ?>">Đã hủy</a>
    </div>

    <?php if (count($orders) > 0) { ?>
        <?php foreach ($orders as $order) { 
            // Lấy 1 sản phẩm đại diện để hiện hình ảnh
            $orderId = $order['id'];
            $sqlItem = "SELECT Product.thumbnail, Product.title, Order_Details.num, Order_Details.price 
                        FROM Order_Details LEFT JOIN Product ON Order_Details.product_id = Product.id 
                        WHERE order_id = $orderId LIMIT 1";
            $firstItem = executeResult($sqlItem, true);
            
            // Đếm tổng số món
            $sqlCount = "SELECT count(id) as count FROM Order_Details WHERE order_id = $orderId";
            $countItem = executeResult($sqlCount, true)['count'];
            $moreText = ($countItem > 1) ? " và ".($countItem - 1)." sản phẩm khác" : "";
        ?>
            <div class="order-item">
                <div class="order-header">
                    <div>
                        <b>Đơn hàng: &nbsp;</b><span>#<?=$order['id']?></span>
                        <span class="ordered-at"><i class="fa-regular fa-clock"></i>&nbsp;&nbsp;Đặt lúc: <?= date('H:i, d/m/Y', strtotime($order['order_date'])) ?></span>
                    </div>
                    <div>
                        <?= getStatusText($order['status']) ?>
                    </div>
                </div>
                
                <div class="order-body">
                    <a href="order_detail.php?id=<?= $order['id'] ?>" style="text-decoration: none; color: inherit; flex-grow: 1;">
                        <div class="product-preview">
                            <img src="<?= $firstItem['thumbnail'] ?>" alt="">
                            <div>
                                <div style="font-weight: 500; margin-bottom: 5px;"><?= $firstItem['title'] ?></div>
                                <div style="font-size: 13px; color: #888;"><?= $moreText ?></div>
                            </div>
                        </div>
                    </a>

                    <div style="text-align: right; min-width: 150px;">
                        <div style="font-size: 13px; color: #666;">Tổng tiền:</div>
                        <div class="total-price"><?= number_format($order['total_money']) ?>đ</div>
                        <div style="margin-top: 10px;">
                            <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn-detail">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-cart-flatbed"></i><br>
            <p><b>Rất tiếc, không tìm thấy đơn hàng nào phù hợp</b></p>
            <a href="index.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php } ?>
</div>

<?php require_once('layouts/footer.php'); ?>
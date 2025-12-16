<?php
require_once('layouts/header.php');
// Giả lập dữ liệu giỏ hàng để test (Bạn hãy xóa đoạn này khi chạy thật với session của bạn)
// $_SESSION['cart'] = []; // Bỏ comment dòng này để test trường hợp Giỏ hàng trống
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng & Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* 1. Phần cuộn chuột nhưng ẩn thanh scrollbar */
        .cart-scroll-container {
            max-height: 400px; /* Chiều cao cố định để kích hoạt cuộn */
            overflow-y: auto;  /* Cho phép cuộn dọc */
            
            /* Ẩn thanh cuộn trên Chrome, Safari, Edge */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE 10+ */
        }
        .cart-scroll-container::-webkit-scrollbar { 
            display: none; /* Chrome/Safari/Webkit */
        }

        /* 2. Style cho item trong giỏ hàng */
        .cart-item {
            background: #fff;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        .item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        /* 3. Style cho phần Empty Cart */
        .empty-cart-icon {
            width: 150px;
            margin-bottom: 20px;
        }

        /* 4. Các tinh chỉnh khác */
        .section-header {
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .btn-checkout {
            background-color: #000; /* Màu đỏ giống hình */
            color: white;
            font-weight: bold;
        }
        .btn-checkout:hover {
            background-color: #fff;
            color: black;
            box-shadow: inset 0 0 0 2px #000
        }
        .bg-light-gray {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-light-gray">

<div class="container py-5">
    <form action="process_order.php" method="POST"> <div class="row">
            
            <div class="col-md-7 pe-md-5">
                <h5 class="section-header">Thông tin đơn hàng</h5>
                <div class="mb-3">
                    <input type="text" class="form-control" name="fullname" placeholder="Họ và tên" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="phone" placeholder="Số điện thoại" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="address" placeholder="Địa chỉ" required>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" name="note" placeholder="Ghi chú thêm (Ví dụ: giao hàng giờ hành chính)"></textarea>
                </div>

                <h5 class="section-header mt-4">Phương thức vận chuyển</h5>
                <div class="card p-3 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="shipping" id="freeship" checked>
                        <label class="form-check-label" for="freeship">
                            Freeship đơn hàng
                        </label>
                    </div>
                </div>

                <h5 class="section-header mt-4">Hình thức thanh toán</h5>
                <div class="card p-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment" id="cod" value="cod" checked>
                        <label class="form-check-label" for="cod">
                            <i class="fas fa-money-bill-wave text-warning me-2"></i> Thanh toán khi giao hàng (COD)
                        </label>
                        <div class="text-muted small ms-4">
                            - Khách hàng được kiểm tra hàng trước khi nhận hàng.<br>
                            - Freeship đơn từ 299K
                        </div>
                    </div>
                    <hr>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment" id="vnpay" value="vnpay">
                        <label class="form-check-label" for="vnpay">
                            <i class="fas fa-qrcode text-primary me-2"></i> Ví điện tử VNPAY
                        </label>
                    </div>
                    <hr>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment" id="momo" value="momo">
                        <label class="form-check-label" for="momo">
                            <i class="fas fa-wallet text-danger me-2"></i> Thanh toán MoMo
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mt-4 mt-md-0">
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng</h5>
                        <span class="badge bg-secondary"><?= !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?> Sản phẩm</span>
                    </div>
                    <hr>

                    <?php if (empty($_SESSION['cart'])): ?>
                        
                        <div class="text-center py-4">
                            <img src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/cart/9bdd8040b334d31946f49e36beaf32db.png" class="empty-cart-icon" alt="Empty Cart">
                            <p class="fw-bold mb-1">Hiện giỏ hàng của bạn không có sản phẩm nào!</p>
                            <p class="text-muted small">Về trang cửa hàng để chọn mua sản phẩm bạn nhé!!</p>
                            <a href="category.php" class="btn btn-outline-dark mt-3">Mua sắm ngay</a>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span><?= number_format(0, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5">Tổng:</span>
                                <span class="fw-bold fs-4 text-danger"><?= number_format(0, 0, ',', '.') ?>₫</span>
                            </div>
                            
                            <button type="submit" class="btn btn-checkout w-100 py-3 text-uppercase">Thanh Toán</button>
                        </div>

                    <?php else: ?>

                        <div class="cart-scroll-container">
                            <?php 
                            $total_price = 0;
                            foreach ($_SESSION['cart'] as $id => $item): 
                                $subtotal = $item['price'] * $item['num'];
                                $total_price += $subtotal;
                            ?>
                            <div class="row cart-item align-items-center">
                                <div class="col-3">
                                    <img src="<?= $item['thumbnail'] ?>" class="item-img" alt="<?= $item['title'] ?>">
                                </div>
                                <div class="col-9">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-0 fw-bold"><?= $item['title'] ?></h6>
                                        <a href="remove_cart.php?id=<?= $id ?>" class="text-danger"><i class="fas fa-times"></i></a>
                                    </div>
                                    <div class="text-muted small my-1">
                                        <p>Size: <?= isset($item['size']) ? $item['size'] : 'FreeSize' ?></p> 
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="input-group input-group-sm" style="width: 100px;">
                                            <button class="btn btn-outline-secondary" type="button">-</button>
                                            <input type="text" class="form-control text-center" value="<?= $item['num'] ?>">
                                            <button class="btn btn-outline-secondary" type="button">+</button>
                                        </div>
                                        <div class="fw-bold"><?= number_format($item['price'], 0, ',', '.') ?>₫</div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span><?= number_format($total_price, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5">Tổng:</span>
                                <span class="fw-bold fs-4 text-danger"><?= number_format($total_price, 0, ',', '.') ?>₫</span>
                            </div>
                            
                            <button type="submit" class="btn btn-checkout w-100 py-3 text-uppercase">Thanh Toán</button>
                        </div>

                    <?php endif; ?>
                    </div>
            </div>
        </div>
    </form>
</div>

<?php
require_once('layouts/footer.php');
?>
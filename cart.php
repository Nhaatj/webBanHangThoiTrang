<?php
require_once('layouts/header.php');

// Nếu user đã đăng nhập, luôn tải lại giỏ hàng mới nhất từ Database 
// -> đảm bảo đồng bộ với các thiết bị khác.
if ($user != null) {
    loadCartFromDB($user['id']);
}

// Kiểm tra đăng nhập
$isLoggedIn = isset($user) && $user != null;
$fullname = $isLoggedIn ? $user['fullname'] : '';
$email = $isLoggedIn ? $user['email'] : '';
$phone_number = $isLoggedIn ? $user['phone_number'] : '';
$address = $isLoggedIn ? $user['address'] : '';

// --- TÍNH TOÁN TỔNG TIỀN VÀ PHÍ SHIP NGAY ĐẦU FILE ---
$total_money = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_money += $item['discount'] * $item['num'];
    }
}

// Logic phí vận chuyển
$shipping_fee = 20000; // Mặc định phí ship 20k
if ($total_money >= 299000) {
    $shipping_fee = 0; // Trên 299k thì miễn phí
}

// Nếu giỏ hàng trống thì phí ship hiển nhiên là 0
if ($total_money == 0) {
    $shipping_fee = 0;
}

// Tổng tiền thanh toán cuối cùng
$final_total = $total_money + $shipping_fee;
// ----------------------------------------------------------------
?>

<style>
    .cart-scroll-container {
        max-height: 450px;
        overflow-y: auto;
        padding-right: 5px;
        scrollbar-width: none;
    }
    .cart-scroll-container::-webkit-scrollbar { 
        display: none; 
    }
    
    .cart-item {
        background: #fff;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .item-img {
        width: 80px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #eee;
    }
    
    .qty-input {
        width: 40px;
        text-align: center;
        border: 1px solid #ddd;
        height: 30px;
        font-size: 14px;
    }
    .qty-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
    }

    .cart-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-top: 20px;
    }
    
    .section-title {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 16px;
        margin-bottom: 15px;
        border-bottom: 2px solid #000;
        padding-bottom: 5px;
        display: inline-block;
    }

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
    .btn-checkout:disabled {
        background-color: #ccc;
        border-color: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="container" style="margin-bottom: 50px;">
    <ul class="breadcrumb" style="background: transparent; padding-left: 0;">
        <li class="breadcrumb-item"><a href="index.php" style="color: #333; text-decoration: none;">Trang Chủ</a></li>
        <li class="breadcrumb-item active">Giỏ hàng & Thanh toán</li>
    </ul>

    <form action="process_order.php" method="POST">
        <input type="hidden" id="shipping_fee" name="shipping_fee" value="<?= $shipping_fee ?>">
        
        <div class="row">
            <div class="col-md-7 mb-4">
                <h4 class="section-title">Thông tin đơn hàng</h4>
                <div class="form-group">
                    <input type="text" class="form-control" name="fullname" placeholder="Họ và tên người nhận" value="<?=$fullname?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <input type="text" class="form-control" name="phone_number" placeholder="Số điện thoại" value="<?=$phone_number?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email (nếu có)" value="<?=$email?>">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label style="font-weight: 600;">Hình thức nhận hàng:</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="method_pickup" name="delivery_method" value="pickup" class="custom-control-input" onchange="toggleAddress(this)">
                        <label class="custom-control-label" for="method_pickup">Nhận tại cửa hàng</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="method_delivery" name="delivery_method" value="delivery" class="custom-control-input" checked onchange="toggleAddress(this)">
                        <label class="custom-control-label" for="method_delivery">Giao tận nơi</label>
                    </div>
                    <div class="form-group" id="address_group">
                        <input type="text" class="form-control" name="address" id="address_input" placeholder="Địa chỉ giao hàng" value="<?=$address?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <textarea class="form-control" name="note" rows="2" placeholder="Ghi chú..."></textarea>
                </div>
            
                <h4 class="section-title mt-4">Hình thức thanh toán</h4>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="p-3 border-bottom">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="payment_cod" name="payment_method" value="COD" class="custom-control-input" checked>
                                <label class="custom-control-label font-weight-bold" for="payment_cod">
                                    <i class="fas fa-money-bill-wave text-warning mr-2"></i> Thanh toán khi giao hàng (COD)
                                </label>
                                <div class="text-muted small ml-4 mt-1">
                                    - Khách hàng được kiểm tra hàng trước khi nhận hàng.<br>
                                    - Freeship đơn từ 299K
                                </div>
                            </div>
                        </div>

                        <div class="p-3 border-bottom">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="payment_vnpay" name="payment_method" value="VNPAY" class="custom-control-input">
                                <label class="custom-control-label font-weight-bold" for="payment_vnpay">
                                    <i class="fas fa-qrcode text-primary mr-2"></i> VNPAY / Banking
                                </label>
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="payment_momo" name="payment_method" value="MOMO" class="custom-control-input">
                                <label class="custom-control-label font-weight-bold" for="payment_momo">
                                    <i class="fas fa-wallet text-danger mr-2"></i> Ví MoMo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="bg-white rounded shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 font-weight-bold" style="font-size: 18px;">
                            <i class="fas fa-shopping-cart mr-2"></i>Giỏ hàng
                        </h4>
                        <span class="badge badge-secondary badge-pill"><?= !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?> sản phẩm</span>
                    </div>
                    <hr class="mt-0">

                    <?php if (empty($_SESSION['cart'])): ?>
                        
                        <div class="text-center py-4">
                            <img src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/cart/9bdd8040b334d31946f49e36beaf32db.png" class="empty-cart-icon" alt="Empty Cart" style="width: 100px;">
                            <p class="fw-bold mb-1 font-weight-bold">Hiện giỏ hàng của bạn không có sản phẩm nào!</p>
                            <p class="text-muted small">Về trang cửa hàng để chọn mua sản phẩm bạn nhé!!</p>
                            <a href="category.php" class="btn btn-outline-dark mt-3">Mua sắm ngay</a>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span>0₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5 font-weight-bold">Tổng:</span>
                                <span class="fw-bold fs-4 text-danger font-weight-bold" style="font-size: 20px;">0₫</span>
                            </div>
                            <button type="submit" class="btn btn-checkout w-100 py-3 text-uppercase" disabled>Thanh Toán</button>
                        </div>

                    <?php else: ?>

                        <div class="cart-scroll-container">
                            <?php 
                            // Ở đây chỉ cần lặp để hiển thị, vì tổng tiền đã tính ở đầu file
                            foreach ($_SESSION['cart'] as $index => $item):
                                // Lấy tồn kho thực tế của sản phẩm/size này
                                $stock = 0;
                                if (!empty($item['size'])) {
                                    $sql = "select inventory_num from Product_Size where product_id = " . $item['id'] . " and size_name = '" . $item['size'] . "'";
                                    $res = executeResult($sql, true);
                                    if($res) $stock = $res['inventory_num'];
                                } else {
                                    $sql = "select inventory_num from Product where id = " . $item['id'];
                                    $res = executeResult($sql, true);
                                    if($res) $stock = $res['inventory_num'];
                                }
                            ?>
                            <div class="cart-item">
                                <div class="d-flex">
                                    <div class="mr-3">
                                        <img src="<?= fixUrl($item['thumbnail'], '') ?>" class="item-img" alt="<?= $item['title'] ?>">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold mb-1" style="font-size: 14px;">
                                                <a href="detail.php?id=<?=$item['id']?>" class="text-dark text-decoration-none"><?= $item['title'] ?></a>
                                            </h6>
                                            <a href="api/cart_actions.php?action=delete&index=<?=$index?>" class="text-danger ml-2"><i class="fas fa-times"></i></a>
                                        </div>
                                        <div class="text-muted small mb-2">
                                            <?php if(isset($item['size']) && $item['size'] != ''): ?>
                                                Size: <span class="badge badge-light border text-dark"><?= $item['size'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="qty-btn" onclick="updateQuantity(<?=$index?>, -1, <?= $stock ?>)">-</button>
                                                <input type="text" id="qty_<?=$index?>" name="" class="qty-input" value="<?= $item['num'] ?>" readonly>
                                                <button type="button" class="qty-btn" onclick="updateQuantity(<?=$index?>, 1, <?= $stock ?>)">+</button>
                                            </div>
                                            <?php
                                                $total_price_items = $item['discount'] * $item['num'];
                                            ?>
                                            <div class="font-weight-bold text-right">
                                                <div style="font-size: 15px;"><?= number_format($total_price_items, 0, ',', '.') ?>₫</div>
                                            </div>
                                        </div>
                                        <div id="err_<?=$index?>" style="color: red; font-size: 12px; font-style: italic; display: none;"></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span><?= number_format($total_money, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <?php if($shipping_fee == 0): ?>
                                    <span class="text-success font-weight-bold">Miễn phí</span>
                                <?php else: ?>
                                    <span><?= number_format($shipping_fee, 0, ',', '.') ?>₫</span>
                                <?php endif; ?>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="font-weight-bold" style="font-size: 18px;">Tổng:</span>
                                <span class="font-weight-bold text-danger" style="font-size: 20px;"><?= number_format($final_total, 0, ',', '.') ?>₫</span>
                            </div>
                            
                            <button type="submit" class="btn btn-checkout w-100 py-3 text-uppercase">Thanh Toán</button>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function updateQuantity(index, delta, maxStock) {
        var input = $('#qty_' + index);
        var currentQty = parseInt(input.val());
        var newQty = currentQty + delta;
        var errBox = $('#err_' + index);

        // Xóa lỗi cũ
        errBox.hide().text('');
        
        if (newQty < 1) {
            // Logic xóa sản phẩm (gọi api delete)
            location.href = 'api/cart_actions.php?action=delete&index=' + index;
            return;
        }

        if (newQty > maxStock) {
            errBox.text('Kho chỉ còn ' + maxStock + ' sản phẩm').show();
                // Tự động ẩn sau 3 giây
                setTimeout(function() {
                    errBox.fadeOut();
                }, 3000);
            return; // Không cho tăng
        }
        window.location.href = 'api/cart_actions.php?action=update&index=' + index + '&delta=' + delta;
    }

    // Script xử lý sự kiện khi người dùng bấm nút Back trình duyệt
    window.addEventListener("pageshow", function(event) {
        // Kiểm tra xem trang có được load từ cache (bfcache) hay không
        var historyTraversal = event.persisted || 
                               (typeof window.performance != "undefined" && 
                                window.performance.navigation.type === 2);
        
        if (historyTraversal) {
            // Nếu là back từ trang khác về, ép reload lại trang để cập nhật giỏ hàng mới nhất
            window.location.reload();
        }
    });

    function toggleAddress(radio) {
        var addressGroup = document.getElementById('address_group');
        var addressInput = document.getElementById('address_input');
        
        // Cập nhật hiển thị phí ship bên cột phải
        var shippingFeeSpan = document.querySelector('.cart-summary .mb-2:nth-child(2) span:last-child');
        var totalMoneySpan = document.querySelector('.cart-summary .mb-3 span.text-danger');
        
        // Lấy tổng tiền hàng (Tạm tính) từ PHP render ra hoặc DOM
        // Cách đơn giản: Lấy text tạm tính và parse ra số
        var subTotalText = document.querySelector('.cart-summary .mb-2:nth-child(1) span:last-child').innerText;
        var subTotal = parseInt(subTotalText.replace(/\./g, '').replace('₫', ''));
        
        var originalShippingFee = <?= $shipping_fee ?>; // Phí ship tính từ PHP (dựa trên 299k)

        if (radio.value == 'pickup') {
            // Ẩn địa chỉ
            addressGroup.style.display = 'none';
            addressInput.removeAttribute('required'); // Bỏ bắt buộc nhập
            addressInput.value = "Cửa hàng M&N"; // Tự điền giá trị

            // Cập nhật UI: Phí ship = 0
            if(shippingFeeSpan) shippingFeeSpan.innerHTML = '<span class="text-success font-weight-bold">Miễn phí</span>';
            if(totalMoneySpan) totalMoneySpan.innerText = new Intl.NumberFormat('vi-VN').format(subTotal) + '₫';
            
        } else {
            // Hiện địa chỉ
            addressGroup.style.display = 'block';
            addressInput.setAttribute('required', 'true');
            addressInput.value = "<?=$address?>"; // Trả lại địa chỉ cũ nếu có

            // Cập nhật UI: Trả lại phí ship gốc
            if(originalShippingFee == 0) {
                if(shippingFeeSpan) shippingFeeSpan.innerHTML = '<span class="text-success font-weight-bold">Miễn phí</span>';
                if(totalMoneySpan) totalMoneySpan.innerText = new Intl.NumberFormat('vi-VN').format(subTotal) + '₫';
            } else {
                if(shippingFeeSpan) shippingFeeSpan.innerText = new Intl.NumberFormat('vi-VN').format(originalShippingFee) + '₫';
                if(totalMoneySpan) totalMoneySpan.innerText = new Intl.NumberFormat('vi-VN').format(subTotal + originalShippingFee) + '₫';
            }
        }
    }
</script>

<?php
require_once('layouts/footer.php');
?>
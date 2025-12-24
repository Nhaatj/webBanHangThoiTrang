<?php
require_once('layouts/header.php');

$productId = getGet('id');
$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.id = $productId";
$product = executeResult($sql, true);

$category_id = $product['category_id'];
$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = $category_id and Product.id != $productId and Product.deleted = 0 order by Product.updated_at desc limit 0,5";
$latestItems = executeResult(sql: $sql);

// Lấy danh sách size của sản phẩm này từ bảng Product_Size
$sqlSize = "SELECT * FROM Product_Size WHERE product_id = $productId AND inventory_num > 0";
$sizeList = executeResult($sqlSize);

// Kiểm tra xem sản phẩm có size không
$hasSize = count($sizeList) > 0;
?>
<style>
  .thumbnail {
    width: 510px; height: 80vh; background-color: #fff; border-radius: 5px; display: flex; justify-content: center;
  }
  .thumbnail img { padding: 10px; }
  .info { width: 610px; height: auto; padding: 15px; background-color: #fff; border-radius: 5px; }
  .row { display: flex; justify-content: space-between; align-items: start; }
  .discount { font-size: 22px; color: red; margin-bottom: 0; }
  .price { font-size: 15px; color: grey; margin-left: 5px; margin-bottom: 0; }
  .no-arrow::-webkit-outer-spin-button, .no-arrow::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
  .no-arrow { -moz-appearance: textfield; }
  
  /* CSS cho nút Size */
  .size-btn { border: 1px solid #ddd; padding: 5px 15px; margin-right: 10px; cursor: pointer; background-color: #fff; font-weight: 500; min-width: 40px; text-align: center; }
  .size-btn:hover { border-color: #000; }
  /* Khi được chọn (Active) */
  .size-btn.active { background-color: #000; color: #fff; border-color: #000; }
  .detail-info {
    padding-top: 0; padding-left: 0; padding-right: 0;
    padding-bottom: 10;
  }
</style>

<div class="container">
    <ul class="breadcrumb" style="text-decoration: none">
      <li class="breadcrumb-item"><a href="index.php" style="text-decoration: none; color:black">Trang Chủ</a></li>
      <li class="breadcrumb-item"><a href="category.php?id=<?= $product['category_id'] ?>" style="text-decoration: none; color:black"><?= $product['category_name'] ?></a></li>
      <li class="breadcrumb-item active"><?= $product['title'] ?></li>
    </ul>

    <div class="row">
        <div class="thumbnail">
            <img src="<?= $product['thumbnail'] ?>" style="width: auto; height: 100%">
        </div>
        <div class="info">
            <h2 style="font-size: 24px"><?= $product['title'] ?></h2>
            <div style="display: flex; align-items: center; margin-top: 10px;">
                <div style="color: #ffa726; margin-right: 15px; display: flex; align-items: center;">
                    <span style="margin-right: 5px; font-weight: bold; font-size: 16px;">5.0</span>
                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                </div>
                <div style="border-left: 1px solid #ccc; height: 20px; margin-right: 15px;"></div>
                <div style="color: #222; font-size: 14px;">7,635 Đã Bán</div>
            </div>
            <?php
            // 1. Xử lý hiển thị Giá (Format chuẩn số tiền Việt Nam: 100.000)
            $formatted_price = number_format($product['discount'], 0, ',', '.');
            // 2. Xử lý Giá cũ (Chỉ hiện nếu có giảm giá)
            $old_price_html = '';
            if($product['price'] > $product['discount']) {
                $old_price_html = '<span class="price"><del>' . number_format($product['price'], 0, ',', '.') . '<u>đ</u></del></span>';
            }
            ?>
            <div style="display: flex; align-items: center; margin-top: 10px">
                <span class="discount"><?= $formatted_price ?><u>đ</u></span>
                <?= $old_price_html ?>
            </div>

            <div class="product-info detail-info">
                <?php if ($hasSize) { ?>
                    <div style="margin-top: 15px;">
                        <div style="display: flex; align-items: center; margin-bottom: 5px;">
                            <p style="font-weight: bold; margin-bottom: 0;">Size:</p>
                            <div id="msg_size_error" style="color: red; font-size: 13px; font-style: italic; margin: 0 10px; display: none;"></div>
                        </div>
                        <div class="size-options mb-3" style="display: flex;">
                            <?php foreach ($sizeList as $item) { ?>
                                <span class="size-btn" 
                                      data-size="<?= $item['size_name'] ?>" 
                                      data-qty="<?= $item['inventory_num'] ?>"
                                      onclick="selectSize(this)">
                                    <?= $item['size_name'] ?>
                                </span>
                            <?php } ?>
                        </div>
                        <input type="hidden" name="size" id="selected_size" value="">
                    </div>
                <?php } else { ?>
                    <input type="hidden" name="size" id="selected_size" value="">
                <?php } ?>
                
                <div style="display: flex; align-items: center;">
                    <p style="margin-bottom: 0; margin-right: 10px;">
                        Số lượng tồn kho: 
                        <span id="inventory_display" style="font-weight: bold; color: #d0021b;">
                            <?= $product['inventory_num'] ?>
                        </span>
                    </p>
                    <div id="msg_num_error" style="color: red; font-size: 13px; font-style: italic; margin: 0; display: none; margin: 0;"></div>
                </div>

                <div style="display: flex; align-items: center; margin-top: 20px; justify-content: space-between; height: 45px">
                    <div style="display: flex; align-items: center; height: 100%;">
                        <button class="btn btn-light" style="height: 100%; border: solid #e0dede 1px; border-radius: 0px; font-weight: bold;" onclick="updateQty(-1)">-</button>
                        <input class="form-control no-arrow" type="number" name="num" id="num_input" step="1" value="1" style="height: 100%; max-width: 70px; border: solid #e0dede 1px; border-radius: 0px; text-align: center; font-weight: bold; background-color: #fff;" readonly>
                        <button class="btn btn-light" style="height: 100%; border: solid #e0dede 1px; border-radius: 0px; font-weight: bold;" onclick="updateQty(1)">+</button>
                    </div>

                    <button class="btn btn-success" style="height: 100%; font-size: 15px; background-color: #000; font-weight: bold; border: 1px solid #000; width: 200px" onclick="addToCart(<?= $product['id'] ?>)">THÊM VÀO GIỎ</button>

                    <button class="btn btn-success" style="height: 100%; font-size: 15px; background-color: #fff; font-weight: bold; border: 1px solid #000; color: #000; width: 180px" onclick="buyNow(<?= $product['id'] ?>)">MUA NGAY</button>
                </div>
            </div>
        </div>

        <div class="col-md-12" style="background-color: #fff; padding: 15px; margin-top: 30px; border-radius: 5px">
            <p style="text-align: center; margin-bottom: 0px; padding-left: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase;">Chi Tiết Sản Phẩm</p>
            <p style="text-align: center; font-size: 12px">&mdash;&mdash;&mdash;&nbsp;&sol;&sol;&sol;&nbsp;&mdash;&mdash;&mdash;</p>
            <p><?= $product['description'] ?></p>
        </div>
    </div>
</div> 

<div class="container">
    <h2 style="text-align: left; margin-top: 30px; margin-bottom: 13px; padding-left: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase; border-left: 3px solid;">Sản phẩm liên quan</h2>
    <div class="product-grid-wrapper">
        <?php foreach($latestItems as $item) { 
            $sizesAttr = htmlspecialchars($item['sizes'], ENT_QUOTES, 'UTF-8');
           
            // 1. Xử lý hiển thị Giá (Format chuẩn số tiền Việt Nam: 100.000)
            $formatted_price = number_format($item['discount'], 0, ',', '.');
            // 2. Xử lý Giá cũ (Chỉ hiện nếu có giảm giá)
            $old_price_html = '';
            if($item['price'] > $item['discount']) {
                $old_price_html = '<span class="product-price"><del>' . number_format($item['price'], 0, ',', '.') . '<u>đ</u></del></span>';
            }
            
            echo '
                <div class="product-item-custom">
                    <a href="detail.php?id='.$item['id'].'" style="text-decoration: none; color: inherit;">
                        <div class="product-img-box">
                            <img src="'.$item['thumbnail'].'" alt="'.$item['title'].'">
                            <div class="hover-overlay">
                                <div class="hover-icon"><i class="fa fa-search"></i></div>
                            </div>
                        </div>
                        <div class="product-info">
                            <div style="display: flex; justify-content: space-between; align-items: center">
                                    <span style="font-size: 12px; color: #888; margin-bottom: 2px;">'.$item['category_name'].'</span>
                            </div>
                            <p class="product-title">'.$item['title'].'</p>
                            <div style="display: flex; align-items: center; justify-content: space-between">
                                <div>
                                    <span class="product-discount">'.$formatted_price.'<u>đ</u></span>
                                    '.$old_price_html.'
                                </div>
                                <button style="border: none; background-color: transparent" 
                                    onclick="event.preventDefault(); event.stopPropagation(); showQuickView(this)"
                                    data-id="'.$item['id'].'"
                                    data-title="'.$item['title'].'"
                                    data-price="'.number_format($item['price'], 0, ',', '.').'₫"
                                    data-discount="'.number_format($item['discount'], 0, ',', '.').'₫"
                                    data-thumbnail="'.$item['thumbnail'].'"
                                    data-sizes="'.$sizesAttr.'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5"/>
                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                                    </svg>
                                </button>
                            </div>    
                        </div>
                    </a>
                </div>';
        } ?>
    </div>
</div>

<script type="text/javascript">
    // Biến global lưu tổng tồn kho của sản phẩm (cho trường hợp chưa chọn size)
    var totalProductInventory = <?= $product['inventory_num'] ?>; 
    // Biến lưu tồn kho hiện tại (thay đổi khi chọn size)
    var currentMaxQty = totalProductInventory; 
    var hasSize = <?= $hasSize ? 'true' : 'false' ?>;

    function showError(msg, err_type) {
        switch (err_type) {
            case 'size':
                $('#msg_size_error').text(msg).show();
                // Tự động ẩn sau 3 giây
                setTimeout(function() {
                    $('#msg_size_error').fadeOut();
                }, 3000);
                break;
            case 'num':
                $('#msg_num_error').text(msg).show();
                // Tự động ẩn sau 3 giây
                setTimeout(function() {
                    $('#msg_num_error').fadeOut();
                }, 3000);
                break;
        }
    }

    function selectSize(element) {
        // 1. Xóa class active cũ
        $('.size-btn').removeClass('active btn-dark').addClass('btn-outline-dark');
        
        // 2. Active nút mới
        $(element).addClass('active btn-dark').removeClass('btn-outline-dark');
        
        // 3. Lấy dữ liệu từ data attribute
        var sizeName = $(element).data('size');
        var qty = parseInt($(element).data('qty'));
        
        // 4. Cập nhật UI và Logic
        $('#selected_size').val(sizeName);
        $('#inventory_display').text(qty); // Cập nhật số hiển thị
        currentMaxQty = qty; // Cập nhật giới hạn mua
        
        // Reset số lượng về 1 để tránh lỗi logic
        $('#num_input').val(1);
        $('#msg_error').hide(); // Ẩn lỗi cũ nếu có
    }

    function updateQty(delta) {
        var input = $('#num_input');
        var num = parseInt(input.val());
        num += delta;
        
        if (num < 1) num = 1;
        
        // Kiểm tra tồn kho
        if (num > currentMaxQty) {
            showError('Số lượng mua vượt quá tồn kho (' + currentMaxQty + ' sản phẩm)!', 'num');
            num = currentMaxQty;
        } else {
            $('#msg_error').hide();
        }
        input.val(num);
    }

    function addToCart(productId) {
        var num = parseInt($('#num_input').val());
        var size = $('#selected_size').val();

        if (hasSize && (size == '' || size == null)) {
            showError('Vui lòng chọn kích thước sản phẩm!', 'size');
            return;
        }
        
        if (num > currentMaxQty) {
            showError('Số lượng mua vượt quá tồn kho (' + currentMaxQty + ' sản phẩm)!', 'num');
            return;
        }

        // Gọi hàm gốc trong footer.php
        addCart(productId, num, size);
    }

    function buyNow(productId) {
        var num = parseInt($('#num_input').val());
        var size = $('#selected_size').val();

        if (hasSize && (size == '' || size == null)) {
            showError('Vui lòng chọn kích thước sản phẩm!', 'size');
            return;
        }
        
        if (num > currentMaxQty) {
            showError('Số lượng mua vượt quá tồn kho (' + currentMaxQty + ' sản phẩm)!', 'num');
            return;
        }

        $.post('api/ajax_request.php', {
            'action': 'cart',
            'id': productId,
            'num': num,
            'size': size
        }, function(data) {
            location.href = 'cart.php';
        });
    }
</script>

<?php require_once('layouts/footer.php'); ?>
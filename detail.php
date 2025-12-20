<?php
require_once('layouts/header.php');

$productId = getGet('id');
$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.id = $productId";
$product = executeResult($sql, true);

$category_id = $product['category_id'];
$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = $category_id order by Product.updated_at desc limit 0,5";


$latestItems = executeResult(sql: $sql);
?>
<style>
  .thumbnail {
    width: 510px;
    height: 80vh;
    background-color: #fff;
    border-radius: 5px;
    
    display: flex;
    justify-content: center;
  }

  .thumbnail img {
    padding: 10px;
  }

  .info {
    width: 610px;
    height: auto;
    padding: 15px;
    background-color: #fff;
    border-radius: 5px;

  }

  .row {
    display: flex;
    justify-content: space-between;
    align-items: start;
  }

  .discount {
    font-size: 22px;
    color: red;
    margin-bottom: 0;
  }

  .price {
    font-size: 15px;
    color: grey;
    margin-left: 5px;
    margin-bottom: 0;
    
  }

  /* Ẩn mũi tên tăng giảm trên Chrome, Safari, Edge, Opera */
  .no-arrow::-webkit-outer-spin-button,
  .no-arrow::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }

  /* Ẩn mũi tên tăng giảm trên Firefox */
  .no-arrow {
      -moz-appearance: textfield;
  }

  /* CSS cho nút Size */
    .size-btn {
        border: 1px solid #ddd;
        padding: 5px 15px;
        margin-right: 10px;
        cursor: pointer;
        background-color: #fff;
        font-weight: 500;
        min-width: 40px;
        text-align: center;
    }

    .size-btn:hover {
        border-color: #000;
    }

    /* Khi được chọn (Active) */
    .size-btn.active {
        background-color: #000;
        color: #fff;
        border-color: #000;
    }
</style>
<div class="container">
    <ul class="breadcrumb" style="text-decoration: none">
      <li class="breadcrumb-item">
        <a href="index.php" style="text-decoration: none; color:black">Trang Chủ</a>
      </li>
      <li class="breadcrumb-item">
        <a href="category.php?id=<?= $product['category_id'] ?>" style="text-decoration: none; color:black"><?= $product['category_name'] ?></a>
      </li>
      <li class="breadcrumb-item active"><?= $product['title'] ?></li>
    </ul>

    <div class="row">
        <div class="thumbnail">
            <img src="<?= $product['thumbnail'] ?>" style="width: auto;
            height: 100%">
        </div>
        <div class="info">
            <h2 style="font-size: 24px"><?= $product['title'] ?></h2>

            <div style="display: flex; align-items: center; margin-top: 10px;">
                <div style="color: #ffa726; margin-right: 15px; display: flex; align-items: center;">
                    <span style="margin-right: 5px; font-weight: bold; font-size: 16px;">5.0</span>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </div>
                
                <div style="border-left: 1px solid #ccc; height: 20px; margin-right: 15px;"></div>
                
                <div style="color: #222; font-size: 14px;">
                    7,635 Đã Bán
                </div>
            </div>

            <div style="display: flex; align-items: center; margin-top: 10px">
              <p class="discount"><?= number_format($product['discount']) ?><sup><u>đ</u></sup></p>
              <p class="price"><del><?= number_format($product['price']) ?><sup><u>đ</u></sup></del></p>
            </div>

            <?php
            $sizes = [];
            if (!empty($product['sizes'])) {
                $sizes = json_decode($product['sizes'], true);
            }

            if (is_array($sizes) && count($sizes) > 0) {
                // Lấy size đầu tiên làm mặc định
                $defaultSize = $sizes[0];
            ?>
                <div style="margin-top: 15px;">
                    <p style="font-weight: bold; margin-bottom: 5px;">Kích thước:</p>
                    <div style="display: flex;">
                        <?php foreach ($sizes as $index => $size): ?>
                            <span class="size-btn <?= ($index == 0) ? 'active' : '' ?>" 
                                onclick="selectSize(this, '<?=$size?>')">
                                <?=$size?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="selected_size" name="selected_size" value="<?= $defaultSize ?>">
                </div>
            <?php 
            } 
            ?>

            <div style="display: flex; align-items: center; margin-top: 20px; justify-content: space-between; height: 45px">
              <div style="display: flex; align-items: center; height: 100%;">
                <button class="btn btn-light" style="height: 100%; border: solid #e0dede 1px; border-radius: 0px; font-weight: bold;" onclick="addMoreCart(-1)">-</button>
                <input class="form-control no-arrow" type="number" name="num" step="1" value="1" style="height: 100%; max-width: 70px; border: solid #e0dede 1px; border-radius: 0px; text-align: center; font-weight: bold;" readonly onchange="fixCartNum()">
                <button class="btn btn-light" style="height: 100%; border: solid #e0dede 1px; border-radius: 0px; font-weight: bold;" onclick="addMoreCart(1)">+</button>
              </div>

              <button class="btn btn-success" style="height: 100%; font-size: 15px; background-color: #000; font-weight: bold; border: 1px solid #000; width: 200px" onclick="addCartWithSize(<?= $product['id'] ?>)">THÊM VÀO GIỎ</button>

              <button class="btn btn-success" style="height: 100%; font-size: 15px; background-color: #fff; font-weight: bold; border: 1px solid #000; color: #000; width: 180px" onclick="buyNow(<?= $product['id'] ?>)">MUA NGAY</button>
            </div>
        </div>

<!-- Miêu tả của sản phẩm -->
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
        <?php
            foreach($latestItems as $item) {
                // Xử lý an toàn cho chuỗi JSON sizes
                $sizesAttr = htmlspecialchars($item['sizes'], ENT_QUOTES, 'UTF-8');
                
                echo '
                <div class="product-item-custom">
                    <a href="detail.php?id='.$item['id'].'" style="text-decoration: none; color: inherit;">
                        <div class="product-img-box">
                            <img src="'.$item['thumbnail'].'" alt="'.$item['title'].'">
                            
                            <div class="hover-overlay">
                                <div class="hover-icon">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <div style="display: flex; justify-content: space-between; align-items: center">
                                <span style="font-size: 12px; color: #888; margin-bottom: 2px;">'.$item['category_name'].'</span>
                            </div>
                            
                            <p class="product-title">'.$item['title'].'</p>
                            <div style="display: flex; align-items: center; justify-content: space-between">
                                <div>
                                    <span class="product-discount">'.number_format($item['discount']).'<sup><u>đ</u></sup></span>
                                    <span class="product-price"><del>'.number_format($item['price']).'<sup><u>đ</u></sup></del></span>
                                </div>
                                
                                <button style="border: none; background-color: transparent" 
                                    onclick="event.preventDefault(); event.stopPropagation(); showQuickView(this)"
                                    data-id="'.$item['id'].'"
                                    data-title="'.$item['title'].'"
                                    data-price="'.number_format($item['price']).' đ"
                                    data-discount="'.number_format($item['discount']).' đ"
                                    data-thumbnail="'.$item['thumbnail'].'"
                                    data-sizes="'.$sizesAttr.'"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5"/>
                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                                    </svg>
                                </button>
                            </div>    
                        </div>
                    </a>
                </div>';
            }
        ?>
    </div>
</div>

<script type="text/javascript">
    function addMoreCart(delta) {
        num = parseInt($('input[name=num]').val());
        num += delta;
        if (num < 1) num = 1;
        if (num > 999) num = 999;
        $('input[name=num]').val(num);
    }

    function fixCartNum() {
        $('input[name=num]').val(Math.abs($('input[name=num]').val()));
    }

    function selectSize(element, size) {
        // 1. Xóa class active ở tất cả các nút size
        $('.size-btn').removeClass('active');

        // 2. Thêm class active vào nút vừa click
        $(element).addClass('active');

        // 3. Gán giá trị vào input ẩn (để sau này gửi lên server khi thêm vào giỏ)
        $('#selected_size').val(size);

        console.log("Đã chọn size: " + size);
    }

    function addCartWithSize(productId) {
        var num = $('input[name=num]').val();
        var size = $('#selected_size').val(); // Lấy giá trị từ input ẩn

        // Kiểm tra: Nếu sản phẩm có hiển thị nút chọn size mà khách chưa chọn
        // Kiểm tra class .size-btn có tồn tại không để biết sản phẩm có size hay không
        if ($('.size-btn').length > 0 && (size == '' || size == null)) {
            alert('Vui lòng chọn kích thước sản phẩm!');
            return; // Dừng lại, không gửi đi
        }

        // Gọi hàm addCart gốc (được định nghĩa ở layouts/footer.php) để gửi đi
        addCart(productId, num, size);
    }

    // --- MUA NGAY (Thêm vào giỏ xong chuyển tới trang cart luôn) ---
    function buyNow(productId) {
        var num = $('input[name=num]').val();
        var size = $('#selected_size').val();

        // 1. Validate Size
        if ($('.size-btn').length > 0 && (size == '' || size == null)) {
            alert('Vui lòng chọn kích thước sản phẩm!');
            return;
        }

        // 2. Gửi AJAX request để thêm vào giỏ
        $.post('api/ajax_request.php', {
            'action': 'cart',
            'id': productId,
            'num': num,
            'size': size
        }, function(data) {
            // 3. Sau khi server phản hồi thành công thì mới chuyển trang
            location.href = 'cart.php';
        });
    }

    // Chờ website tải xong để đảm bảo hàm test đã tồn tại
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy giá trị thực tế đang nằm trong thẻ input để in ra
        let currentVal = document.getElementById('selected_size').value;
        test(currentVal);
    });
    function test(defSize) { 
        console.log("Size mặc định: " + defSize);
    }
</script>

<?php
require_once('layouts/footer.php');
?>

<!-- 
S,M,L
<span class="size-btn" onclick="selectSize(this, 'S')">S</span>
<span class="size-btn" onclick="selectSize(this, 'M')">M</span>
<span class="size-btn" onclick="selectSize(this, 'L')">L</span>

Chọn <span class="size-btn" onclick="selectSize(this, 'L')">L</span>
  selectSize(element, size):
    $('.size-btn').removeClass('active');
        (Chưa có thẻ chứa class "size-btn" nào active -> Bỏ qua)
    $(element).addClass('active');
        <span class="size-btn active" onclick="selectSize(this, 'L')">L</span>
    $('#selected_size').val(size);
        <input type="hidden" id="selected_size" name="selected_size" value="L"> 

Chọn <span class="size-btn" onclick="selectSize(this, 'M')">M</span>
  selectSize(element, size):
    $('.size-btn').removeClass('active');
        1 thẻ có "active": <span class="size-btn active" onclick="selectSize(this, 'L')">L</span>
        -> Bỏ "active" <span class="size-btn" onclick="selectSize(this, 'L')">L</span>
    $(element).addClass('active');
        <span class="size-btn active" onclick="selectSize(this, 'M')">M</span>
    $('#selected_size').val(size);
        <input type="hidden" id="selected_size" name="selected_size" value="M"> 
-->




<?php
require_once('layouts/header.php');
?>

<!-- Banner START -->
    <div id="demo" class="carousel slide container" data-ride="carousel">

        <!-- Indicators -->
        <ul class="carousel-indicators">
            <li data-target="#demo" data-slide-to="0" class="active"></li>
            <li data-target="#demo" data-slide-to="1"></li>
            <li data-target="#demo" data-slide-to="2"></li>
        </ul>

        <!-- The slideshow -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/photos/banner1.png" alt="banner 1">
            </div>
            <div class="carousel-item">
                <img src="assets/photos/banner2.png" alt="banner 2">
            </div>
            <div class="carousel-item">
                <img src="assets/photos/banner3.png" alt="banner 3">
            </div>
        </div>

        <!-- Left and right controls -->
        <a class="carousel-control-prev" href="#demo" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#demo" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>

    </div>
<!-- Banner STOP -->
    
    <div class="container">
        <h2 style="text-align: left; margin-top: 30px; margin-bottom: 13px; padding-left: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase; border-left: 3px solid;">Sản phẩm mới nhất</h2>

        <img src="assets/photos/banner-hang-moi.jpg" alt="Hàng mới" height="auto" width="100%" style="border-radius: 8px; margin-bottom: 13px">
        
        <div class="product-grid-wrapper">
            <?php
                foreach($latestItems as $item) {
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
                                    <span class="badge badge-light border">Hàng Mới</span>
                                </div>
                                
                                <p class="product-title">'.$item['title'].'</p>
                                <div style="display: flex; align-items: center; justify-content: space-between">
                                    <div>
                                        <span class="product-discount">'.number_format($item['discount']).'<sup><u>đ</u></sup></span>
                                        <span class="product-price"><del>'.number_format($item['price']).'<sup><u>đ</u></sup></del></span>
                                    </div>
                                    <button style="border: none; background-color: transparent" onclick="addCart('.$item['id'].', 1)">
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
        
        <!-- Danh mục sản phẩm -->
        <?php
            foreach($menuItems as $item) {
                $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = ".$item['id']." order by Product.updated_at desc limit 0,10"; 
                // Lưu ý: Tăng limit lên 10 hoặc bỏ limit để test tính năng trượt ngang
                
                $items = executeResult($sql);
                if($items == null || count($items) < 1) continue;
        ?>
        
        <h2 style="text-align: left; margin-top: 30px; margin-bottom: 13px; padding-left: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase; border-left: 3px solid;">Thời trang <?=$item['name']?></h2>
        
        <?php if(!empty($item['banner'])): ?>
            <img src="<?= fixUrl($item['banner'], '') ?>" style="height: auto; width: 100%; border-radius: 8px; margin-bottom: 13px;"/>
        <?php endif; ?>

        <div class="product-list-wrapper owl-carousel owl-theme">
            <?php
                foreach($items as $pItem) {
                    echo '
                    <div class="product-item-custom">
                        <a href="detail.php?id='.$pItem['id'].'" style="text-decoration: none; color: inherit;">
                            <div class="product-img-box">
                                <img src="'.$pItem['thumbnail'].'">
                                <div class="hover-overlay">
                                    <div class="hover-icon">
                                        <i class="fa fa-search"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="product-info">
                                <p style="font-size: 12px; color: #888; margin-bottom: 2px;">'.$pItem['category_name'].'</p>
                                <p class="product-title">'.$pItem['title'].'</p>
                                <div style="display: flex; align-items: center; justify-content: space-between">
                                    <div>
                                        <span class="product-discount">'.number_format($pItem['discount']).'<sup><u>đ</u></sup></span>
                                        <span class="product-price"><del>'.number_format($pItem['price']).'<sup><u>đ</u></sup></del></span>
                                    </div>
                                    <button style="border: none; background-color: transparent" onclick="addCart('.$pItem['id'].', 1)">
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

        <div style="display: flex; align-items: center; justify-content: center">
            <a class="button" href="category.php?id=<?=$item['id']?>">Xem tất cả »</a>
        </div>
        <?php
        }
        ?>
    </div>

<script>
    $(document).ready(function(){
        $(".product-list-wrapper").owlCarousel({
            loop: false,        // Không lặp lại vô tận
            margin: 15,         // Khoảng cách giữa các sản phẩm
            nav: false,         // Tắt mũi tên điều hướng (Next/Prev)
            dots: true,         // BẬT BULLETS (Dấu chấm)
            mouseDrag: true,    // BẬT NẮM KÉO CHUỘT
            touchDrag: true,    // Bật cảm ứng vuốt trên điện thoại
            responsive: {
                0: {
                    items: 2    // Điện thoại: Hiện 2 sản phẩm
                },
                600: {
                    items: 3    // Tablet: Hiện 3 sản phẩm
                },
                1000: {
                    items: 5    // Máy tính: Hiện 5 sản phẩm
                }
            }
        });
    });
</script>
<?php
require_once('layouts/footer.php');
?>
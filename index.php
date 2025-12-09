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
        <h2 style="text-align: center; margin-top: 20px; margin-bottom: 20px;">SẢN PHẨM MỚI NHẤT</h2>
        <div class="row">
            <?php
                foreach($latestItems as $item) {
                    echo '<div class="col-md-3 col-6 product-item">
                            <img src="'.$item['thumbnail'].'" style="width: 100%; height: 220px">
                            <p style="font-weight: 500;">'.$item['category_name'].'</p>
                            <p style="font-weight: 500;">'.$item['title'].'</p>
                            <p style="color: red; font-weight: 500;">'.number_format($item['discount']).' đ</p>
                        </div>';
                }
            ?>
        </div>
        
        <!-- Danh mục sản phẩm -->
        <?php
        foreach($menuItems as $item) {
            $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = ".$item['id']." order by Product.updated_at desc limit 0,4";
            $items = executeResult(sql: $sql);
            if($items == null || count($items) < 4) continue;
        ?>
        <h2 style="text-align: center; margin-top: 20px; margin-bottom: 20px;"><?=$item['name']?></h2>
        <div class="row">
            <?php
                foreach($items as $pItem) {
                    echo '<div class="col-md-3 col-6 product-item">
                            <img src="'.$pItem['thumbnail'].'" style="width: 100%; height: 220px">
                            <p style="font-weight: 500;">'.$pItem['category_name'].'</p>
                            <p style="font-weight: 500;">'.$pItem['title'].'</p>
                            <p style="color: red; font-weight: 500;">'.number_format($pItem['discount']).' đ</p>
                        </div>';
                }
            ?>
        </div>
        <?php
        }
        ?>
    </div>

<?php
require_once('layouts/footer.php');
?>
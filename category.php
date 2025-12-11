<?php
require_once('layouts/header.php');

$category_id = getGet('id');

if($category_id == null || $category_id == ''){
  $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id order by Product.updated_at desc";
} else {
  $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = $category_id order by Product.updated_at desc";
}

$latestItems = executeResult(sql: $sql);
?> 
<div class="container">
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
                            </div>
                            
                            <p class="product-title">'.$item['title'].'</p>
                            <div style="display: flex; align-items: center;">
                                <span class="product-discount">'.number_format($item['discount']).'đ</span>
                                <span class="product-price"><del>'.number_format($item['price']).'đ</del></span>
                            </div>    
                        </div>
                    </a>
                </div>';
            }
        ?>
    </div>
</div>

<?php
require_once('layouts/footer.php');
?>
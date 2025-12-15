<?php
require_once('layouts/header.php');

$category_id = getGet('id');

if($category_id == null || $category_id == ''){
  $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id order by Product.updated_at desc";
} else {
  $sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = $category_id order by Product.updated_at desc";
}

$latestItems = executeResult($sql);
?> 
<div class="container">
    <img src="assets/photos/banner-hang-moi.jpg" alt="Hàng mới" height="auto" width="100%" style="border-radius: 8px; margin-bottom: 13px">
    
    <div class="product-grid-wrapper">
        <?php
            foreach($latestItems as $item) {
                // Xử lý chuỗi JSON sizes an toàn
                $sizesAttr = isset($item['sizes']) ? htmlspecialchars($item['sizes'], ENT_QUOTES, 'UTF-8') : '';
                
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
                                    <span class="product-discount">'.number_format($item['discount']).'<u>đ</u></span>
                                    <span class="product-price"><del>'.number_format($item['price']).'<u>đ</u></del></span>
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

<?php
require_once('layouts/footer.php');
?>
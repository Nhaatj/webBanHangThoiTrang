<?php
require_once('../utils/utility.php');
require_once('../database/dbhelper.php');

// Lấy từ khóa từ request POST
$keyword = getPost('keyword');

if (!empty($keyword)) {
    // Tìm sản phẩm có tên chứa từ khóa, giới hạn 5 kết quả
    $sql = "SELECT * FROM Product WHERE title LIKE '%$keyword%' AND deleted = 0 LIMIT 5";
    $result = executeResult($sql);

    if (count($result) > 0) {
        echo '<div class="search-heading">Sản phẩm gợi ý</div>';

        // --- Mở thẻ bao quanh để cuộn ---
        echo '<div class="search-scroll-container">';
        
        foreach ($result as $item) {
            // 1. Xử lý hiển thị Giá
            $price_display = number_format($item['discount'], 0, ',', '.') . '₫';
            $old_price_display = ($item['price'] > $item['discount']) ? '<del>' . number_format($item['price'], 0, ',', '.') . '₫</del>' : '';
            
            // 2. Xử lý hiển thị Ảnh
            $thumbnail = fixUrl($item['thumbnail'], ''); 

            // 3. Xử lý hiển thị Size (Code mới thêm)
            $size_html = '';
            if (isset($item['sizes']) && $item['sizes'] != '') {
                // Giải mã JSON: ["S", "M"] -> Array
                $sizesArr = json_decode($item['sizes'], true);
                
                // Nếu là mảng hợp lệ thì nối thành chuỗi "S, M"
                if (is_array($sizesArr) && count($sizesArr) > 0) {
                    $sizesStr = implode(', ', $sizesArr);
                    $size_html = '<div class="search-item-size">Size: ' . $sizesStr . '</div>';
                }
            }

            echo '
            <a href="detail.php?id=' . $item['id'] . '" class="search-item">
                <div class="search-item-img">
                    <img src="' . $thumbnail . '" alt="' . $item['title'] . '">
                </div>
                <div class="search-item-info">
                    <div style="display: flex; align-items: center; justify-content: space-between">
                        <div class="search-item-name">' . $item['title'] . '</div>

                        ' . $size_html . '
                    </div>
                    
                    <div class="search-item-price">
                        <span class="new-price">' . $price_display . '</span>
                        <span class="old-price">' . $old_price_display . '</span>
                    </div>
                </div>
            </a>';
        }
        echo '</div>'; // Đóng thẻ search-scroll-container
    } else {
        echo '<div class="search-no-result">Không tìm thấy sản phẩm nào.</div>';
    }
}
?>
<?php
require_once('../utils/utility.php');
require_once('../database/dbhelper.php');

// Lấy từ khóa từ request POST
$keyword = getPost('keyword');

if (!empty($keyword)) {
    // 1. Tìm sản phẩm (JOIN bảng Product_Size để lấy size)
    // Sử dụng GROUP_CONCAT để gộp các size lại thành chuỗi "S, M, L" ngay trong câu SQL
    $sql = "SELECT p.*, GROUP_CONCAT(ps.size_name SEPARATOR ', ') as size_list
            FROM Product p
            LEFT JOIN Product_Size ps ON p.id = ps.product_id AND ps.inventory_num > 0
            WHERE p.title LIKE '%$keyword%' AND p.deleted = 0
            GROUP BY p.id
            LIMIT 5";
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

            // 3. Xử lý hiển thị Size
            // Lấy trực tiếp từ cột size_list đã GROUP_CONCAT ở trên
            $size_html = '';
            if (!empty($item['size_list'])) {
                $size_html = '<div class="search-item-size">Size: ' . $item['size_list'] . '</div>';
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
<?php
require_once('layouts/header.php');

// Lấy các tham số từ URL
$search = getGet('search');
// $category_id = getGet('category_id');
$sort = getGet('sort'); // price_asc, price_desc, latest

// Lấy mảng category_ids từ URL (nếu không có thì là mảng rỗng)
$category_ids = isset($_GET['category_ids']) ? $_GET['category_ids'] : [];
// --- Lấy mảng size đã chọn ---
$selected_sizes = isset($_GET['sizes']) ? $_GET['sizes'] : [];

$productList = [];

if($search != '') {
    // Xây dựng câu truy vấn SQL động
    $sql = "SELECT Product.*, Category.name as category_name 
        FROM Product 
        LEFT JOIN Category ON Product.category_id = Category.id 
        WHERE Product.title LIKE '%$search%' 
        AND Product.deleted = 0";

    // Nếu có chọn danh mục thì thêm điều kiện lọc
    // if ($category_id != '' && $category_id > 0) {
    //     $sql .= " AND Product.category_id = $category_id";
    // }

    // --- LỌC DANH MỤC ---
    if (!empty($category_ids)) {
        // Bảo mật: Ép kiểu tất cả phần tử về số nguyên để tránh SQL Injection
        $ids_sanitized = array_map('intval', $category_ids);
        
        // Nối mảng thành chuỗi: [1, 2] -> "1,2"
        $ids_string = implode(',', $ids_sanitized);
        
        // Dùng toán tử IN để tìm sản phẩm thuộc BẤT KỲ danh mục nào trong danh sách
        $sql .= " AND Product.category_id IN ($ids_string)";
    }

    // --- LỌC THEO SIZE ---
    // Logic: Sản phẩm được chọn nếu nó có chứa ÍT NHẤT 1 trong các size người dùng tick
    if (!empty($selected_sizes)) {
        $size_conditions = [];
        foreach ($selected_sizes as $size) {
            // Vì trong DB lưu dạng JSON ["S","M"], nên ta tìm chuỗi chứa "S" (bao gồm dấu nháy)
            // để tránh nhầm lẫn (ví dụ tìm L mà ra XL)
            $size_conditions[] = "Product.sizes LIKE '%\"$size\"%'";
        }
        // Nối các điều kiện bằng OR và bao lại trong ngoặc đơn
        // Ví dụ: AND (sizes LIKE '%"S"%' OR sizes LIKE '%"M"%')
        $sql .= " AND (" . implode(' OR ', $size_conditions) . ")";
    }

    // Xử lý sắp xếp
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY Product.discount ASC"; // Giá tăng dần (theo giá khuyến mãi)
            break;
        case 'price_desc':
            $sql .= " ORDER BY Product.discount DESC"; // Giá giảm dần
            break;
        default:
            $sql .= " ORDER BY Product.updated_at DESC"; // Mặc định: Mới nhất
            break;
    }

    $productList = executeResult($sql);
}

?>

<div class="container" style="margin-bottom: 50px;">
    <ul class="breadcrumb" style="text-decoration: none; background: transparent; padding-left: 0;">
        <li class="breadcrumb-item"><a href="index.php" style="color: #333; text-decoration: none;">Trang Chủ</a></li>
        <li class="breadcrumb-item active">Tìm kiếm: <?= htmlspecialchars($search) ?></li>
    </ul>

    <!-- Sort -->
    <div class="row" style="margin-bottom: 20px; align-items: center;">
        <div class="col-md-12">
            <form action="search.php" method="get" id="filterForm">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                
                <div class="form-row align-items-center" style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
                    <div class="col-auto">
                        <span style="font-weight: bold;"><i class="fa fa-filter"></i> Bộ lọc:</span>
                    </div>
                    
                    <!-- Menu lọc -->
                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: white;">
                                Tùy chọn lọc
                                <?php 
                                    $total_filter = count($category_ids) + count($selected_sizes);
                                    if($total_filter > 0) echo "($total_filter)"; 
                                ?>
                            </button>
                            
                            <div class="dropdown-menu p-3" aria-labelledby="dropdownMenuButton" style="min-width: 250px; max-height: 400px; overflow-y: auto;">
                                
                                <h6 class="dropdown-header font-weight-bold text-dark px-0" style="text-transform: uppercase;">Danh mục:</h6>
                                <?php
                                foreach ($menuItems as $item) {
                                    $checked = in_array($item['id'], $category_ids) ? 'checked' : '';
                                    echo '
                                    <div class="custom-control custom-checkbox mb-2 ml-2">
                                        <input type="checkbox" class="custom-control-input" 
                                            id="cat_'.$item['id'].'" 
                                            name="category_ids[]" 
                                            value="'.$item['id'].'" 
                                            '.$checked.'>
                                        <label class="custom-control-label" for="cat_'.$item['id'].'">'.$item['name'].'</label>
                                    </div>';
                                }
                                ?>

                                <div class="dropdown-divider"></div>

                                <h6 class="dropdown-header font-weight-bold text-dark px-0" style="text-transform: uppercase;">Kích cỡ:</h6>
                                <div class="d-flex flex-wrap ml-2">
                                    <?php
                                    // Danh sách size cố định (giống trong trang Admin)
                                    $listSizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                    
                                    foreach ($listSizes as $size) {
                                        $sizeChecked = in_array($size, $selected_sizes) ? 'checked' : '';
                                        echo '
                                        <div class="custom-control custom-checkbox mr-3 mb-2">
                                            <input type="checkbox" class="custom-control-input" 
                                                id="size_'.$size.'" 
                                                name="sizes[]" 
                                                value="'.$size.'" 
                                                '.$sizeChecked.'>
                                            <label class="custom-control-label" for="size_'.$size.'">'.$size.'</label>
                                        </div>';
                                    }
                                    ?>
                                </div>

                                <div class="dropdown-divider"></div>
                                
                                <button type="submit" class="btn btn-sm btn-primary btn-block">Áp dụng</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-auto">
                        <select name="sort" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="latest" <?= ($sort == 'latest') ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                            <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                    
                    <div class="col-auto">
                         <a href="search.php?search=<?= htmlspecialchars($search) ?>" class="btn btn-sm btn-outline-secondary">Xóa bộ lọc</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert -->
    <div style="margin-bottom: 15px;">
        <?php if ($search == '') { ?>
            <div class="alert alert-warning">Vui lòng nhập từ khóa để tìm kiếm.</div>
        <?php } elseif (count($productList) == 0) { ?>
            
            <?php 
                // Kiểm tra xem có đang dùng bộ lọc (Category hoặc Size) hay không
                $has_filter = !empty($category_ids) || !empty($selected_sizes);
            ?>

            <?php if ($has_filter) { ?>
                <div class="alert alert-secondary">
                    <h5 style="font-size: 16px; font-weight: bold;">Rất tiếc!</h5>
                    <p class="mb-0">Không tìm thấy sản phẩm nào phù hợp với từ khóa <b>"<?= htmlspecialchars($search) ?>"</b> và các bộ lọc bạn đã chọn.</p>
                    <p class="mb-0" style="font-size: 14px; margin-top: 5px;">
                        <i>Gợi ý: Hãy thử bỏ bớt các tiêu chí lọc (như kích cỡ hoặc danh mục) để xem nhiều kết quả hơn.</i>
                    </p>
                </div>
            <?php } else { ?>
                <div class="alert alert-info">
                    Không tìm thấy sản phẩm nào có tên chứa từ khóa <b>"<?= htmlspecialchars($search) ?>"</b>.
                </div>
            <?php } ?>

        <?php } else { ?>
            <p style="color: #555; font-size: 16px;">Tìm thấy <b><?= count($productList) ?></b> sản phẩm cho từ khóa "<b><?= htmlspecialchars($search) ?></b>":</p>
        <?php } ?>
    </div>
    
    <!-- Hiện sản phẩm -->
    <div class="product-grid-wrapper">
        <?php
            foreach($productList as $item) {
                // 1. Xử lý hiển thị Giá (Format chuẩn số tiền Việt Nam: 100.000)
                    $formatted_price = number_format($item['discount'], 0, ',', '.');
                    
                // 2. Xử lý Giá cũ (Chỉ hiện nếu có giảm giá)
                $old_price_html = '';
                if($item['price'] > $item['discount']) {
                    $old_price_html = '<span class="product-price"><del>' . number_format($item['price'], 0, ',', '.') . '<u>đ</u></del></span>';
                }

                // Xử lý chuỗi JSON sizes an toàn
                $sizesAttr = isset($item['sizes']) ? htmlspecialchars($item['sizes'], ENT_QUOTES, 'UTF-8') : '';
                
                // Xử lý hiển thị giá
                $price_display = number_format($item['discount'], 0, ',', '.').'<u>đ</u>';
                $old_price_display = '<del>'.number_format($item['price'], 0, ',', '.').'<u>đ</u></del>';

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
                                    <span class="product-discount">'.$formatted_price.'<u>đ</u></span>
                                    '.$old_price_html.'
                                </div>
                                
                                <button style="border: none; background-color: transparent" 
                                    onclick="event.preventDefault(); event.stopPropagation(); showQuickView(this)"
                                    data-id="'.$item['id'].'"
                                    data-title="'.$item['title'].'"
                                    data-price="'.number_format($item['price'], 0, ',', '.').' đ"
                                    data-discount="'.number_format($item['discount'], 0, ',', '.').' đ"
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

<script>
    // Ngăn dropdown đóng khi click vào bên trong nó
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });
</script>

<?php require_once('layouts/footer.php'); ?>
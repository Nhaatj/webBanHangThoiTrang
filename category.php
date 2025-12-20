<?php
require_once('layouts/header.php');

// 1. Lấy các tham số từ URL
$category_id = getGet('id');
$sort = getGet('sort');

// Lấy các bộ lọc
$selected_sizes = isset($_GET['sizes']) ? $_GET['sizes'] : [];
$selected_cats = isset($_GET['category_ids']) ? $_GET['category_ids'] : [];

// 2. Lấy thông tin Danh mục hiện tại (để hiện Banner và Breadcrumb)
$category = [];
if($category_id != '' && $category_id > 0) {
    $category = executeResult("select * from Category where id = $category_id", true);
}

// 3. Xây dựng câu SQL lấy sản phẩm
$sql = "SELECT Product.*, Category.name as category_name 
        FROM Product 
        LEFT JOIN Category ON Product.category_id = Category.id 
        WHERE Product.deleted = 0";

// --- LOGIC PHÂN CHIA TRƯỜNG HỢP ---

if ($category_id != '' && $category_id > 0) {
    // TRƯỜNG HỢP 1: Đang ở trang danh mục cụ thể (Ví dụ: Nam) -> Cố định category_id
    $sql .= " AND Product.category_id = $category_id";
} else {
    // TRƯỜNG HỢP 2: Đang ở trang "Tất cả sản phẩm" -> Cho phép lọc theo nhiều danh mục
    if (!empty($selected_cats)) {
        $ids_sanitized = array_map('intval', $selected_cats);
        $ids_string = implode(',', $ids_sanitized);
        $sql .= " AND Product.category_id IN ($ids_string)";
    }
}

// --- LOGIC LỌC SIZE (Dùng chung cho cả 2 trường hợp) ---
if (!empty($selected_sizes)) {
    $size_conditions = [];
    foreach ($selected_sizes as $size) {
        $size_conditions[] = "Product.sizes LIKE '%\"$size\"%'";
    }
    $sql .= " AND (" . implode(' OR ', $size_conditions) . ")";
}

// --- LOGIC SẮP XẾP ---
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY Product.discount ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY Product.discount DESC";
        break;
    default:
        $sql .= " ORDER BY Product.updated_at DESC";
        break;
}

$productList = executeResult($sql)

?> 
<div class="container">
    <ul class="breadcrumb" style="text-decoration: none; padding-left: 0;">
        <li class="breadcrumb-item">
            <a href="index.php" style="text-decoration: none; color:black">Trang Chủ</a>
        </li>
        <?php if(count($category) > 0) {?>
            <li class="breadcrumb-item active"><?= $category['name'] ?></li>
        <?php } else { ?>
            <li class="breadcrumb-item active">Tất Cả Sản Phẩm</li>       
        <?php } ?>
    </ul>
    
    <?php if(!empty($category) && $category['banner'] != '') {?>
        <img src="<?= fixUrl($category['banner'], '') ?>" alt="<?= $category['name'] ?>" height="auto" width="100%" style="border-radius: 8px; margin-bottom: 20px">
    <?php } else { ?>
        <img src="assets/photos/banner-hang-moi.jpg" alt="Sản phẩm" height="auto" width="100%" style="border-radius: 8px; margin-bottom: 20px">
    <?php } ?>
    
    <div class="row mb-3 align-items-center">
        <div class="col-md-12">
            <form action="category.php" method="get">
                <?php if($category_id != '') { ?>
                    <input type="hidden" name="id" value="<?=$category_id?>">
                <?php } ?>

                <div class="form-row align-items-center" style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
                    <div class="col-auto">
                        <span style="font-weight: bold;"><i class="fa fa-filter"></i> Bộ lọc:</span>
                    </div>

                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownFilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: white;">
                                Tùy chọn lọc
                                <?php 
                                    $count_filter = count($selected_sizes);
                                    if(empty($category_id)) { // Nếu là trang tất cả, đếm thêm danh mục đã lọc
                                        $count_filter += count($selected_cats);
                                    }
                                    if($count_filter > 0) echo "($count_filter)";
                                ?>
                            </button>
                            
                            <div class="dropdown-menu p-3" aria-labelledby="dropdownFilter" style="min-width: 250px;">
                                
                                <?php if(empty($category_id)) { ?>
                                    <h6 class="dropdown-header font-weight-bold text-dark px-0">DANH MỤC:</h6>
                                    <?php
                                    // $menuItems lấy từ header.php
                                    $sqlMenu = "select * from Category";
                                    $menuList = executeResult($sqlMenu);
                                    foreach ($menuList as $item) {
                                        $checked = in_array($item['id'], $selected_cats) ? 'checked' : '';
                                        echo '
                                        <div class="custom-control custom-checkbox mb-2 ml-2">
                                            <input type="checkbox" class="custom-control-input" id="c_'.$item['id'].'" name="category_ids[]" value="'.$item['id'].'" '.$checked.'>
                                            <label class="custom-control-label" for="c_'.$item['id'].'">'.$item['name'].'</label>
                                        </div>';
                                    }
                                    ?>
                                    <div class="dropdown-divider"></div>
                                <?php } ?>

                                <h6 class="dropdown-header font-weight-bold text-dark px-0">KÍCH CỠ:</h6>
                                <div class="d-flex flex-wrap ml-2">
                                    <?php
                                    $listSizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                    foreach ($listSizes as $size) {
                                        $sizeChecked = in_array($size, $selected_sizes) ? 'checked' : '';
                                        echo '
                                        <div class="custom-control custom-checkbox mr-3 mb-2">
                                            <input type="checkbox" class="custom-control-input" id="s_'.$size.'" name="sizes[]" value="'.$size.'" '.$sizeChecked.'>
                                            <label class="custom-control-label" for="s_'.$size.'">'.$size.'</label>
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
                         <a href="category.php<?= ($category_id != '') ? '?id='.$category_id : '' ?>" class="btn btn-sm btn-outline-secondary">Xóa bộ lọc</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div style="margin-bottom: 15px;">
        <?php if (count($productList) == 0) { ?>
            <?php 
                // Kiểm tra xem có đang dùng bộ lọc hay không
                $has_filter = !empty($selected_sizes) || !empty($selected_cats);
            ?>

            <?php if ($has_filter) { ?>
                <div class="alert alert-secondary text-center py-4">
                    <h5 style="font-weight: bold;">Không tìm thấy sản phẩm!</h5>
                    <p class="mb-0">Rất tiếc, không có sản phẩm nào phù hợp với bộ lọc bạn đã chọn.</p>
                    <p class="small text-muted mt-2">Hãy thử bỏ bớt tiêu chí lọc (ví dụ chọn size khác) để xem kết quả.</p>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning text-center py-4">
                    <h5 style="font-weight: bold;">Danh mục trống!</h5>
                    <p class="mb-0">Hiện tại chưa có sản phẩm nào trong danh mục này.</p>
                    <a href="index.php" class="btn btn-dark btn-sm mt-3">Quay lại trang chủ</a>
                </div>
            <?php } ?>

        <?php } else { ?>
            <?php } ?>
    </div>

    <div class="product-grid-wrapper">
        <?php
            foreach($productList as $item) {
                // 1. Xử lý hiển thị Giá (Format chuẩn số tiền Việt Nam: 100.000)
                    $formatted_price = number_format($item['discount'], 0, ',', '.');
                    
                // 2. Xử lý Giá cũ (Chỉ hiện nếu có giảm giá)
                $old_price_html = '';
                if($item['price'] > $item['discount']) {
                    $old_price_html = '<span class="product-price"><del>' . number_format($item['price'], 0, ',', '.') . '<sup><u>đ</u></sup></del></span>';
                }

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
                                    <span class="product-discount">'.$formatted_price.'<sup><u>đ</u></sup></span>
                                    '.$old_price_html.'
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

<script>
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });
</script>

<?php
require_once('layouts/footer.php');
?>
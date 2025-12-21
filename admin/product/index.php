<?php
$title = 'Quản Lý Sản Phẩm';
$baseUrl = '../';
require_once('../layouts/header.php');

$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.deleted = 0";
$data = executeResult($sql);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12 table-responsive">
        <div style="display: flex; justify-content: space-between; align-items: center">
            <h3 style="margin-bottom: 0;">Quản Lý Sản Phẩm</h3>
        
            <a href="editor.php"><button class="btn btn-success">Thêm Sản Phẩm</button></a>
        </div>

        <table class="table table-bordered table-hover" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 50px">STT</th>
                    <th style="width: 80px">Thumbnail</th>
                    <th>Tên Sản Phẩm</th>
                    <th style="width: 100px">Danh Mục</th>
                    <th style="width: 150px">Size</th>
                    <th style="width: 100px">Giá Gốc</th>
                    <th style="width: 100px">Giá Giảm</th>
                    <th style="width: 50px"></th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;
                foreach ($data as $item) {
                    // --- XỬ LÝ SIZE ---
                        $sizeStr = '';
                        if (!empty($item['sizes'])) {
                            $sizes = json_decode($item['sizes'], true);
                            if (is_array($sizes)) {
                                $sizeStr = implode(', ', $sizes); // Nối mảng thành chuỗi: S, M, L
                            }
                        }
                    // ------------------

                    echo '<tr>
                            <td>' . (++$index) . '</td>
                            <td><img src="' . fixUrl($item['thumbnail'], '../../') . '" style="height: 100px;"/></td>
                            <td>' . $item['title'] . '</td>
                            <td>' . $item['category_name'] . '</td>
                            <td>' . $sizeStr . '</td>
                            <td class="text-right">' . number_format($item['price']). '₫</td>
                            <td class="text-right">' . number_format($item['discount']). '₫</td>
                            <td style="width: 50px">
                                <a href="editor.php?id=' . $item['id'] . '"><button class="btn btn-warning">Sửa</button></a>
                            </td>
                            <td style="width: 50px">
                                <button onclick="deleteProduct(' . $item['id'] . ')" class="btn btn-danger">Xóa</button>         
                            </td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function deleteProduct(id) {
        option = confirm('Bạn có chắc muốn xóa sản phẩm này không?')
        if (!option) return;

        $.post(
            'form_api.php', 
            {
                'id': id,
                'action': 'delete'
            },
            function(data) {
                location.reload()
            }
        )
    }
</script>

<?php
require_once('../layouts/footer.php');
?>
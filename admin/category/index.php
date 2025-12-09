<?php
$title = 'Quản Lý Danh Mục Sản Phẩm';
$baseUrl = '../';
require_once('../layouts/header.php');

$id = $name = $banner = '';
require_once('form_save.php');
if (isset($_GET['id'])) {
    $id = getGet('id');
    $sql = "select * from Category where id = '$id'";
    $data = executeResult($sql, true);

    if ($data != null) {
        $name = $data['name']; 
        $banner = $data['banner'];
    }
}

$sql = "select * from Category";
$data = executeResult($sql);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <h3 style="margin-bottom: 16px;">Quản Lý Danh Mục Sản Phẩm</h3>
    </div>    
    <div class="col-md-6">
        <form method="post" action="index.php" onsubmit="return validateForm();" style="font-weight: 700" enctype="multipart/form-data">
            <div class="form-group">
                <label for="usr">Tên Danh Mục:</label>
                <input required="true" type="text" class="form-control" id="usr" name="name" value="<?= $name ?>">
                <input type="text" name="id" value="<?= $id ?>" hidden="true">
                <div class="form-group" style="margin-bottom: 8px; margin-top: 16px">
                    <label for="banner_url">Banner (File/URL):</label>
                    <input type="file" class="form-control" id="banner" name="banner" accept=".avif, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" onchange="updateThumbnail()" style="margin-bottom: 10px;">
                    <input type="text" class="form-control" id="banner_url" name="banner_url" value="<?= (strpos($banner, 'http') !== false ? $banner : '') ?>" placeholder="Nhập URL..." oninput="updateThumbnail()">
                </div>
                <div class="form-group">
                    <img id="banner_img" src="<?= ($banner != '') ? fixUrl($banner, '../../') : 'https://placehold.co/600x400?text=No+Image' ?>" style="max-height: 160px; margin-top: 5px; margin-bottom: 10px; max-width: 100%; object-fit: contain; border: 1px solid #ccc;">
                </div>
            </div>
            <button class="btn btn-success" style="background-color: rgba(0, 0, 255, 0.621);  width: 20%; font-weight: bold">Lưu</button>
        </form>
    </div>    
    <div class="col-md-6 table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Danh Mục</th>
                    <th>Banner</th>
                    <th style="width: 50px"></th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;
                foreach ($data as $item) {
                    echo '<tr>
                            <td>' . (++$index) . '</td>
                            <td>' . $item['name'] . '</td>
                            <td><img src="' . fixUrl($item['banner'], '../../') . '" style="height: 100px; width: 100%"/></td>
                            <td style="width: 50px">
                                <a href="?id=' . $item['id'] . '"><button class="btn btn-warning">Sửa</button></a>
                            </td>
                            <td style="width: 50px">
                                <button onclick="deleteCategory(' . $item['id'] . ')" class="btn btn-danger">Xóa</button>
                            </td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function deleteCategory(id) {
        option = confirm('Bạn có chắc muốn xóa danh mục sản phẩm này không?')
        if (!option) return;

        $.post(
            'form_api.php', 
            {
                'id': id,
                'action': 'delete'
            }, 
            function(data) {
                if (data != null && data != '') {
                    alert(data);
                    return;
                }
                location.reload()
            }
        )
    }
</script>
<script>
    function updateThumbnail() {
        var fileInput = document.getElementById('banner');
        var urlInput = document.getElementById('banner_url');
        var img = document.getElementById('banner_img');

        // Ưu tiên: Nếu có chọn file thì đọc file
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result; // Cập nhật ảnh preview bằng dữ liệu file
            }
            reader.readAsDataURL(fileInput.files[0]);
        }
        // Nếu không chọn file mà có nhập URL
        else if (urlInput.value && urlInput.value.trim() !== '') {
            img.src = urlInput.value; // Cập nhật ảnh preview bằng URL
        }
        // Nếu không có gì cả
        else {
            // Giữ nguyên ảnh cũ hoặc về ảnh mặc định (tuỳ nhu cầu), ở đây ta để về placeholder
            // Tuy nhiên logic tốt nhất là nếu field trống thì giữ nguyên hiển thị cũ (do PHP load), 
            // nhưng để đơn giản ta hiển thị placeholder để báo hiệu đang trống.
            img.src = "https://placehold.co/600x400?text=No+Image";
        }
    }
</script>

<?php
require_once('../layouts/footer.php');
?>
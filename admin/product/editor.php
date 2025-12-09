<?php
$title = 'Thêm/Sửa Sản Phẩm';
$baseUrl = '../';
require_once('../layouts/header.php');

$id = $thumbnail = $title = $price = $discount = $category_id = $description = '';
require_once('form_save.php');

$id = getGet('id');
if ($id != '' && $id > 0) {
    $sql = "select * from Product where id = '$id' and deleted = 0";
    $userItem = executeResult($sql, true);
    if ($userItem != null) {
        $thumbnail = $userItem['thumbnail'];
        $title = $userItem['title'];
        $price = $userItem['price'];
        $discount = $userItem['discount'];
        $category_id = $userItem['category_id'];
        $description = $userItem['description'];
    } else {
        $id = 0;
    }
} else {
    $id = 0;
}

$sql = "select * from Category";
$categoryItems = executeResult($sql);
?>
<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12 table-responsive">
        <h3>Thêm/Sửa Sản Phẩm</h3>
        <div class="panel panel-primary">
            <div class="panel-body">
                <form method="post" style="font-weight: 700" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-9 col-12">
                            <div class="form-group">
                                <label for="usr">Tên Sản Phẩm:</label>
                                <input required="true" type="text" class="form-control" id="usr" name="title" value="<?= $title ?>">
                                <input type="text" name="id" value="<?= $id ?>" hidden="true">
                            </div>
                            <div class="form-group">
                                <label for="description">Mô tả:</label>
                                <textarea class="form-control" rows="5" name="description" id="description"><?= $description ?></textarea>
                            </div>
                            <button class="btn btn-success" style="background-color: rgba(0, 0, 255, 0.621);  width: 20%; font-weight: bold">Lưu</button>
                        </div>
                        <div class="col-md-3 col-12">

                            <div class="form-group" style="margin-bottom: 8px">
                                <!-- <label for="thumbnail">Thumbnail:</label>
                                <input required="true" type="text" class="form-control" id="thumbnail" name="thumbnail" value="<?= $thumbnail ?>" onchange="updateThumbnail()">
                                <img id="thumbnail_img" src="<?= $thumbnail ?>" style="max-height: 200px; margin-top: 5px; margin-bottom: 10px; max-width: 100%;"> -->
                                <label for="thumbnail_url">Thumbnail (File/URL):</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept=".avif, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" onchange="updateThumbnail()" style="margin-bottom: 10px;">
                                <!-- <img id=" thumbnail_img" src="<?= fixUrl($thumbnail, '../../') ?>" style="max-height: 200px; margin-top: 5px; margin-bottom: 10px; max-width: 100%;"> -->
                                <input type="text" class="form-control" id="thumbnail_url" name="thumbnail_url" value="<?= (strpos($thumbnail, 'http') !== false ? $thumbnail : '') ?>" placeholder="Nhập URL..." oninput="updateThumbnail()">
                            </div>

                            <div class="form-group">
                                <img id="thumbnail_img" src="<?= ($thumbnail != '') ? fixUrl($thumbnail, '../../') : 'https://placehold.co/600x400?text=No+Image' ?>" style="max-height: 160px; margin-top: 5px; margin-bottom: 10px; max-width: 100%; object-fit: contain; border: 1px solid #ccc;">
                            </div>

                            <div class="form-group">
                                <label for="category_id">Danh Mục Sản Phẩm:</label>
                                <select class="form-control" name="category_id" id="category_id" required="true">
                                    <option value="">-- Chọn --</option>
                                    <?php
                                    foreach ($categoryItems as $item) {
                                        if ($item['id'] == $category_id) {
                                            echo '<option selected value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                        } else {
                                            echo '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Giá:</label>
                                <input required="true" type="number" class="form-control" id="price" name="price" value="<?= $price ?>">
                            </div>
                            <div class="form-group">
                                <label for="discount">Giảm Giá:</label>
                                <input required="true" type="number" class="form-control" id="discount" name="discount" value="<?= $discount ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#description').summernote({
        placeholder: 'Hello stand alone ui',
        tabsize: 2,
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
</script>

<script>
    function updateThumbnail() {
        var fileInput = document.getElementById('thumbnail');
        var urlInput = document.getElementById('thumbnail_url');
        var img = document.getElementById('thumbnail_img');

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
<!-- <script type="text/javascript">
    function updateThumbnail() {
        $('#thumbnail_img').attr('src', $('#thumbnail').val())
    }
</script> -->

<?php
require_once('../layouts/footer.php');
?>
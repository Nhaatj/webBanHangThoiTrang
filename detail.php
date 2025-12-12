<?php
require_once('layouts/header.php');

$productId = getGet('id');
$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.id = $productId";
$product = executeResult($sql, true);

$category_id = $product['category_id'];
$sql = "select Product.*, Category.name as category_name from Product left join Category on Product.category_id = Category.id where Product.category_id = $category_id order by Product.updated_at desc limit 0,5";


$latestItems = executeResult(sql: $sql);
?>
<style>
  .breadcrumb{
    padding: 0 5px;
    padding-left: 0;
    background-color: transparent;
    margin-bottom: 8px;
  }

  .thumbnail {
    width: 510px;
    height: 80vh;
    background-color: #fff;
    border-radius: 5px;
    
    display: flex;
    justify-content: center;
  }

  .thumbnail img {
    padding: 10px;
  }

  .info {
    width: 610px;
    height: auto;
    padding: 15px;
    background-color: #fff;
    border-radius: 5px;

  }

  .row {
    display: flex;
    justify-content: space-between;
    align-items: start;
  }

  .discount {
    font-size: 22px;
    color: red;
    margin-bottom: 0;
  }

  .price {
    font-size: 15px;
    color: grey;
    margin-left: 5px;
    margin-bottom: 0;
  }

  /* Ẩn mũi tên tăng giảm trên Chrome, Safari, Edge, Opera */
  .no-arrow::-webkit-outer-spin-button,
  .no-arrow::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }

  /* Ẩn mũi tên tăng giảm trên Firefox */
  .no-arrow {
      -moz-appearance: textfield;
  }
</style>
<div class="container">
    <ul class="breadcrumb" style="text-decoration: none">
      <li>
        <a href="index.php" style="text-decoration: none; color:black">Trang Chủ</a>&nbsp;&nbsp;/&nbsp;&nbsp;
      </li>
      <li>
        <a href="category.php?id=<?= $product['category_id'] ?>" style="text-decoration: none; color:black"> <?= $product['category_name'] ?></a>&nbsp;&nbsp;/&nbsp;&nbsp;
      </li>
      <li> <?= $product['title'] ?></li>
    </ul>

    <div class="row">
        <div class="thumbnail">
            <img src="<?= $product['thumbnail'] ?>" style="width: auto;
            height: 100%">
        </div>
        <div class="info">
            <h2 style="font-size: 24px"><?= $product['title'] ?></h2>

            <div style="display: flex; align-items: center; margin-top: 10px;">
                <div style="color: #ffa726; margin-right: 15px; display: flex; align-items: center;">
                    <span style="margin-right: 5px; font-weight: bold; font-size: 16px;">5.0</span>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </div>
                
                <div style="border-left: 1px solid #ccc; height: 20px; margin-right: 15px;"></div>
                
                <div style="color: #222; font-size: 14px;">
                    7,635 Đã Bán
                </div>
            </div>

            <div style="display: flex; align-items: center; margin-top: 10px">
              <p class="discount"><?= number_format($product['discount']) ?> đ</p>
              <p class="price"><del><?= number_format($product['price']) ?> đ</del></p>
            </div>

            <div style="display: flex; align-items: center; margin-top: 20px; justify-content: space-between; height: 45px">
              <div style="display: flex; align-items: center; height: 100%;">
                <button class="btn btn-light" style="height: 100%; border: solid #e0dede 1px; border-radius: 0px; font-weight: bold;">-</button>
                <input class="form-control no-arrow" type="number" step="1" value="1" style="height: 100%; max-width: 90px; border: solid #e0dede 1px; border-radius: 0px; text-align: center; font-weight: bold;">
                <button class="btn btn-light" style="height: 100%; border: solid #e0dede 1px; border-radius: 0px; font-weight: bold;">+</button>
              </div>

              <button class="btn btn-success" style="height: 100%; font-size: 15px; background-color: #000; font-weight: bold; border: 1px solid #000; width: 200px">THÊM VÀO GIỎ</button>

              <button class="btn btn-success" style="height: 100%; font-size: 15px; background-color: #fff; font-weight: bold; border: 1px solid #000; color: #000; width: 180px">MUA NGAY</button>
            </div>
        </div>

<!-- Miêu tả của sản phẩm -->
        <div class="col-md-12" style="background-color: #fff; padding: 15px; margin-top: 30px; border-radius: 5px">
            <p style="text-align: center; margin-bottom: 0px; padding-left: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase;">Chi Tiết Sản Phẩm</p>
            <p style="text-align: center; font-size: 12px">&mdash;&mdash;&mdash;&nbsp;&sol;&sol;&sol;&nbsp;&mdash;&mdash;&mdash;</p>
            <p><?= $product['description'] ?></p>
        </div>
    </div>
</div> 

<div class="container">

    <h2 style="text-align: left; margin-top: 30px; margin-bottom: 13px; padding-left: 12px; font-size: 18px; font-weight: bold; text-transform: uppercase; border-left: 3px solid;">Sản phẩm liên quan</h2>
    
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


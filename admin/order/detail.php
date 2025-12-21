<?php
$title = 'Thông Tin Chi Tiết Đơn Hàng';
$baseUrl = '../';
require_once('../layouts/header.php');

$orderId = getGet('id');

$sql = "select Order_Details.*, Product.title, Product.thumbnail from Order_Details left join Product on Product.id = Order_Details.product_id where Order_Details.order_id = $orderId";
$data = executeResult($sql);

$total_detail = 0;
foreach ($data as $item) {
    // Tổng tiền sản phẩm trong order details (Không bao gồm phí ship)
    $total_detail += $item['total_money'];
}

$sql = "select * from Orders where id = $orderId";
$orderItem = executeResult($sql, true);
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <h3>Chi Tiết Đơn Hàng</h3>
    </div>

    <div class="col-md-8 table-responsive" style="margin-top: 20px;">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Thumbnail</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá</th>
                    <th>Số Lượng</th>
                    <th>Tổng Giá</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 0;
                foreach ($data as $item) {
                    echo '<tr>
                            <td>' . (++$index) . '</td>
                            <td><img src="'.fixUrl($item['thumbnail'], '../../').'" style="height: 120px"/></td>
                            <td>' . $item['title'] . '</td>
                            <td class="text-right">' . $item['price'] . '₫</td>
                            <td class="text-right">' . $item['num'] . '</td>
                            <td class="text-right">' . $item['total_money'] . '₫</td>
                        </tr>';

                }
                ?>

                <tr>
                  <?php if ($orderItem['address'] == "Nhận tại cửa hàng M&N" || $total_detail >= 299000) {
                      echo '<td style="font-weight: bold" colspan="5" class="text-left">TỔNG CỘNG:</td>';
                  } else {
                      echo '<td style="font-weight: bold" colspan="5" class="text-left">TỔNG CỘNG (Bao gồm phí vận chuyển):</td>';
                  }?>
                  <th class="text-right"><?=$orderItem['total_money']?>₫</th>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4" style="margin-top: 20px;">
      <table class="table table-bordered table-hover">
        <tr>
          <th>Họ & Tên:</th>
          <td><?=$orderItem['fullname']?></td>
        </tr>
        <tr>
          <th>Email:</th>
          <td><?=$orderItem['email']?></td>
        </tr>
        <tr>
          <th>Địa Chỉ:</th>
          <td><?=$orderItem['address']?></td>
        </tr>
        <tr>
          <th>Phone:</th>
          <td><?=$orderItem['phone_number']?></td>
        </tr>
      </table>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<?php
$title = 'Dashboard';
$baseUrl = '';
$titleHeader = 'Dashboard';
require_once('layouts/header.php');

// 1. Số lượng Khách hàng (role_id = 2)
$sql = "SELECT COUNT(*) as count FROM User WHERE role_id = 2 AND deleted = 0";
$cntUser = executeResult($sql, true)['count'];

// 2. Số lượng Sản phẩm (deleted = 0)
$sql = "SELECT COUNT(*) as count FROM Product WHERE deleted = 0";
$cntProduct = executeResult($sql, true)['count'];

// 3. Tổng Doanh thu (Chỉ tính đơn hàng thành công: status = 2)
// Lưu ý: Nếu database chưa có đơn thành công nào, sum sẽ trả về null -> ép về 0
$sql = "SELECT SUM(total_money) as total FROM Orders WHERE status = 2";
$revenueItem = executeResult($sql, true);
$revenue = $revenueItem['total'] != null ? $revenueItem['total'] : 0;

// 4. Tổng Đơn hàng (Tất cả đơn hàng)
$sql = "SELECT COUNT(*) as count FROM Orders";
$cntOrder = executeResult($sql, true)['count'];

// 5. Dữ liệu cho Biểu đồ (Thống kê sản phẩm theo Danh mục)
$sql = "SELECT Category.name, COUNT(Product.id) as count 
        FROM Product 
        JOIN Category ON Product.category_id = Category.id 
        WHERE Product.deleted = 0 
        GROUP BY Category.id";
$chartData = executeResult($sql);

$chartLabels = [];
$chartCounts = [];
foreach ($chartData as $item) {
    $chartLabels[] = $item['name'];
    $chartCounts[] = $item['count'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .dashboard-card {
        background-color: #fff;
        border: 1px solid rgba(0,0,0,0.08); /* Viền đen nhẹ */
        border-radius: 10px; /* Bo tròn góc */
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Shadow nhẹ bên dưới */
        padding: 25px 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }

    /* Phần nội dung bên trái (Số và Chữ) */
    .dashboard-card .card-info {
        text-align: left;
    }
    
    .dashboard-card .card-info h3 {
        font-size: 20;
        font-weight: 700;
        color: #333; /* Chữ đen */
        margin: 0 0 5px 0;
    }

    .dashboard-card .card-info p {
        font-size: 16px;
        color: #666; /* Chữ xám đậm cho tiêu đề */
        font-weight: 600;
        margin: 0;
    }

    /* Phần Icon bên phải */
    .dashboard-card .card-icon {
        width: 60px;
        height: 60px;
        background-color: rgba(13, 71, 161, 0.1); /* Nền xanh nhạt bao quanh icon */
        border-radius: 50%; /* Icon hình tròn */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-card .card-icon i {
        font-size: 24px;
        color: #0d47a1; /* Màu Dark Blue như yêu cầu */
    }
</style>

<div class="row" style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between;">
    <div class="col-md-6">
        <div class="row">
            
            <div class="col-md-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-info">
                        <h3><?= number_format($cntUser) ?></h3>
                        <p>Thành viên</p>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-info">
                        <h3><?= number_format($cntProduct) ?></h3>
                        <p>Sản phẩm</p>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-info">
                        <h3><?= number_format($revenue, 0, ',', '.') ?><small style="font-size: 16px">₫</small></h3>
                        <p>Doanh thu</p>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">
                <div class="dashboard-card">
                    <div class="card-info">
                        <h3><?= number_format($cntOrder) ?></h3>
                        <p>Đơn hàng</p>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php
        // Format dữ liệu về dạng mảng của mảng: [['Label', 'Value'], ['Giày', 10], ...]
        $dataForChart = [];
        $dataForChart[] = ['Danh mục', 'Số lượng'];
        foreach ($chartData as $item) {
            $dataForChart[] = [$item['name'], (int)$item['count']];
        }
    ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <div class="col-md-6">
        <div class="card shadow mb-4" style="border-radius: 10px; border: 1px solid rgba(0,0,0,0.08);">
            <div class="card-header py-3 bg-white" style="border-bottom: 1px solid rgba(0,0,0,0.05); border-radius: 10px 10px 0 0;">
                <h6 class="m-0 font-weight-bold text-dark">Biểu đồ sản phẩm</h6>
            </div>
            <div class="card-body" style="padding: 0;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px;">
                    
                    <div id="piechart_3d" style="width: 65%; height: 300px;"></div>

                    <div id="custom_legend" style="width: 35%; display: flex; flex-direction: column; justify-content: center;">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // 1. Cấu hình Google Charts
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        // 2. Danh sách màu sắc (Bạn có thể đổi màu tùy thích tại đây)
        const myColors = [
            '#3366cc', // Xanh dương đậm
            '#dc3912', // Đỏ
            '#ff9900', // Cam vàng
            '#109618', // Xanh lá
            '#990099', // Tím
            '#0099c6', // Xanh lơ
            '#dd4477', // Hồng
            '#66aa00'  // Xanh nõn chuối
        ];

        function drawChart() {
            // Lấy dữ liệu từ PHP
            var dataArray = <?= json_encode($dataForChart) ?>;
            var data = google.visualization.arrayToDataTable(dataArray);

            var options = {
                is3D: true,             // Kích hoạt chế độ 3D
                pieSliceText: 'percentage', // Hiển thị phần trăm trên miếng bánh
                legend: 'none',         // Ẩn legend mặc định của Google (để mình tự làm cái đẹp hơn)
                colors: myColors,       // Set bảng màu
                chartArea: {            // Căn chỉnh biểu đồ cho to rõ
                    left: 10, 
                    top: 10, 
                    width: '100%', 
                    height: '90%'
                },
                // Cấu hình font chữ cho phần trăm
                pieSliceTextStyle: {
                    color: 'white',
                    fontSize: 14
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);

            // 3. Tạo Chú thích (Legend) tùy chỉnh để có hình tròn
            generateCustomLegend(dataArray);
        }

        function generateCustomLegend(dataArray) {
            const legendContainer = document.getElementById('custom_legend');
            legendContainer.innerHTML = ''; // Reset

            // Bỏ qua phần tử đầu tiên vì nó là Header ['Danh mục', 'Số lượng']
            for (let i = 1; i < dataArray.length; i++) {
                const label = dataArray[i][0]; // Tên danh mục
                const color = myColors[(i - 1) % myColors.length]; // Lấy màu tương ứng

                // Tạo HTML cho từng dòng chú thích
                const item = document.createElement('div');
                item.style.display = 'flex';
                item.style.alignItems = 'center';
                item.style.marginBottom = '12px'; // Khoảng cách giữa các dòng

                // Tạo hình tròn màu
                const dot = document.createElement('span');
                dot.style.width = '12px';
                dot.style.height = '12px';
                dot.style.backgroundColor = color;
                dot.style.borderRadius = '50%'; // Bo tròn
                dot.style.display = 'inline-block';
                dot.style.marginRight = '10px';

                // Tạo text
                const text = document.createElement('span');
                text.innerText = label;
                text.style.fontSize = '14px';
                text.style.color = '#333';
                text.style.fontWeight = '500';

                item.appendChild(dot);
                item.appendChild(text);
                legendContainer.appendChild(item);
            }
        }
    </script>

<?php
require_once('layouts/footer.php');
?>
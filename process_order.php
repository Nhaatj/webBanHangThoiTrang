<?php
session_start();
require_once('utils/utility.php');
require_once('database/dbhelper.php');
require_once('config_vnpay.php');
require_once('config_momo.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!empty($_POST)) {
    // --- THÊM ĐOẠN CODE KIỂM TRA NÀY ---
    // Nếu giỏ hàng rỗng (do đã thanh toán rồi hoặc hack), đá về trang cart ngay
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        header('Location: cart.php');
        die();
    }
    // ------------------------------------

    // 1. Lấy thông tin từ form
    $fullname = getPost('fullname');
    $email = getPost('email');
    $phone_number = getPost('phone_number');
    $address = getPost('address');
    $note = getPost('note');
    $delivery_method = getPost('delivery_method'); // 'shipping' hoặc 'pickup'
    $payment_method = getPost('payment_method');   // 'COD', 'VNPAY', 'MOMO'

    // 2. Tính toán lại Phí Ship (Server-side validation)
    $total_money = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_money += $item['discount'] * $item['num'];
        }
    }
    $shipping_fee = 0;
    if ($delivery_method == 'pickup') {
        $shipping_fee = 0;
        $address = "Nhận tại cửa hàng M&N"; // Cập nhật cứng địa chỉ nếu là pickup
    } else {
        // Logic tính phí ship giống bên cart.php
        if ($total_money < 299000) {
            $shipping_fee = 20000;
        }
    }

    // 3. Tính tổng tiền cuối cùng
    $final_total = $total_money + $shipping_fee;

    $order_date = date('Y-m-d H:i:s');

    $status = 0; // 0: Chờ xử lý/Chờ thanh toán
    
    $user_id = isset($_SESSION['user']) ? "'".$_SESSION['user']['id']."'" : "NULL"; // NULL nếu khách vãng lai

    // 4. Lưu Đơn Hàng vào DB
    $sql = "INSERT INTO Orders (user_id, fullname, email, phone_number, address, note, order_date, status, total_money, payment_method) 
            VALUES ($user_id, '$fullname', '$email', '$phone_number', '$address', '$note', '$order_date', $status, '$final_total', '$payment_method')";
    execute($sql);

    // Lấy ID đơn hàng vừa tạo để làm mã giao dịch
    $sql = "SELECT id FROM Orders WHERE order_date = '$order_date' ORDER BY id DESC LIMIT 1";
    $orderItem = executeResult($sql, true);
    $orderId = $orderItem['id'];

    // 5. Lưu Chi tiết đơn hàng & trừ tồn kho
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $price = $item['discount'];
            $num = $item['num'];
            $size = isset($item['size']) ? $item['size'] : ''; // Lấy size nếu có
            $total_item = $price * $num;

            // a. Lưu vào Order_Details
            $sql = "INSERT INTO Order_Details (order_id, product_id, price, num, size, total_money) 
                    VALUES ('$orderId', '$product_id', '$price', '$num', '$size', '$total_item')";
            execute($sql);

            // b. Xử lý trừ tồn kho
            if (!empty($size)) {
                // TH1: Sản phẩm có Size
                // 1. Trừ số lượng trong bảng Product_Size
                $sql_size = "UPDATE Product_Size SET inventory_num = inventory_num - $num 
                             WHERE product_id = $product_id AND size_name = '$size'";
                execute($sql_size);

                // 2. Trừ tổng số lượng trong bảng Product (để quản lý tổng quan)
                $sql_total = "UPDATE Product SET inventory_num = inventory_num - $num 
                              WHERE id = $product_id";
                execute($sql_total);
            } else {
                // TH2: Sản phẩm không có Size (chỉ quản lý số lượng ở bảng Product)
                $sql_total = "UPDATE Product SET inventory_num = inventory_num - $num 
                              WHERE id = $product_id";
                execute($sql_total);
            }
        }
    }

    // 6. XỬ LÝ CHUYỂN HƯỚNG THANH TOÁN
    // Xóa giỏ hàng sau khi lưu xong ---------------------------------------------------
    unset($_SESSION['cart']); // Xóa giỏ hàng Session
    
    // Xóa giỏ hàng trong Database nếu đã đăng nhập
    if (isset($_SESSION['user'])) {
        clearCartDB($_SESSION['user']['id']); // Hàm này đã viết ở bước 2
    }
    // --------------------------------------------------------------------------------
    
    // --- TRƯỜNG HỢP 1: COD ---
    if ($payment_method == 'COD') {
        header('Location: checkout.php?order_id=' . $orderId . '&msg=Dat hang thanh cong');
        die();
    }

    // --- TRƯỜNG HỢP 2: VNPAY ---
    if ($payment_method == 'VNPAY') {
        // Cài đặt múi giờ để tránh lỗi Expire Date
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TxnRef = $orderId; // Mã đơn hàng
        // Bỏ ký tự đặc biệt trong OrderInfo để tránh lỗi chữ ký
        $vnp_OrderInfo = "Thanh_toan_don_hang_" . $orderId;
        $vnp_OrderType = "other";
        $vnp_Amount = (int)$final_total * 100; // VNPAY tính đơn vị đồng nhân 100
        $vnp_Locale = "vn";
        // QUAN TRỌNG: Fix cứng IP là 127.0.0.1.
        // IP của bạn đang là ::1 (IPv6), VNPAY Sandbox thường từ chối hoặc mã hóa sai dẫn đến lỗi chữ ký.
        $vnp_IpAddr = "127.0.0.1";

        $vnp_CreateDate = date('YmdHis');
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes', strtotime($vnp_CreateDate)));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_ExpireDate" => $vnp_ExpireDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        header('Location: ' . $vnp_Url);
        die();
    }

    // --- TRƯỜNG HỢP 3: MOMO ---
    if ($payment_method == 'MOMO') {
        $orderIdMoMo = $orderId . "_" . time(); // ID duy nhất: ID-đơn_Thời-gian
        $requestId = time() . "";
        $amount = (string)$final_total; // MoMo cần chuỗi
        $orderInfo = "#".$orderId;
        $extraData = ""; 

        $rawHash = "accessKey=" . $momo_accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $momo_notifyUrl . "&orderId=" . $orderIdMoMo . "&orderInfo=" . $orderInfo . "&partnerCode=" . $momo_partnerCode . "&redirectUrl=" . $momo_returnUrl . "&requestId=" . $requestId . "&requestType=payWithATM";
        $signature = hash_hmac("sha256", $rawHash, $momo_secretKey);

        $data = array(
            'partnerCode' => $momo_partnerCode,
            'partnerName' => "Web Ban Hang",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderIdMoMo,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $momo_returnUrl,
            'ipnUrl' => $momo_notifyUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => 'payWithATM',
            'signature' => $signature
        );

        $result = execPostRequest($momo_endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        if(isset($jsonResult['payUrl'])) {
            header('Location: ' . $jsonResult['payUrl']);
        } else {
            // In lỗi ra màn hình để debug nếu có
            echo "Lỗi MoMo: " . $jsonResult['message']; 
        }
        die();
    }
}

// Hàm gửi request cho MoMo (Đã xóa curl_close để fix warning)
function execPostRequest($url, $data){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    // curl_close($ch); <--- Đã comment dòng này lại vì PHP 8 tự động xử lý
    return $result;
}


    

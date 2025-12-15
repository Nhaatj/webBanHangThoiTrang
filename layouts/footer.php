    <footer class="footer-dark">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <h5><a href="" style="display: inline-block;"><img src="assets/photos/logo.jpg" style="height: 50px; width: 62.5px;"></a></h5>
                    <p>123 Tô ký, Phường Tân Thới Hiệp, Quận 12, TP. HCM</p>
                    <p><strong>Điện thoại:</strong> 033 835 6397</p>`
                    <p><strong>Email:</strong> mnin2025@gmail.com</p>
                    
                    <div class="social-icons">
                        <a href="" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <h5>CÔNG TY</h5>
                    <ul>
                        <li><a href="">M&N</a></li>
                        <li><a href="">Tuyển Dụng & Việc Làm</a></li>
                        <li><a href="">Tin Tức Thời Trang</a></li>
                        <li><a href="">Chăm Sóc Khách Hàng</a></li>
                    </ul>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <h5>CHÍNH SÁCH KHÁCH HÀNG</h5>
                    <ul>
                        <li><a href="">Chính Sách KH Thân Thiết</a></li>
                        <li><a href="">Chính Sách Đổi và Trả Hàng</a></li>
                        <li><a href="">Chính Sách Bảo Hành</a></li>
                        <li><a href="">Chính Sách Bảo Mật</a></li>
                        <li><a href="">Hướng Dẫn Sử Dụng</a></li>
                        <li><a href="">Các Câu Hỏi Thường Gặp</a></li>
                    </ul>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <h5>THÔNG TIN CỬA HÀNG</h5>
                    <div style="margin-bottom: 15px;">
                        <strong style="text-transform: uppercase;">CỬA HÀNG SỐ 1</strong>
                        <p style="margin-top: 5px;">246 Lý Tự Trọng, Phường Bến Nghé, Quận 1, Ho Chi Minh City</p>
                    </div>
                    <a href="" style="text-decoration: underline;">Xem tất cả cửa hàng</a>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; Bản quyền thuộc về M&N
            </div>
        </div>
    </footer>
    <script type="text/javascript">
        function addCart(productId, num, size) {
            // console.log(productId + ", " + num + ", " + size);
            // Nếu size không được truyền vào (ví dụ bấm từ trang chủ), mặc định là chuỗi rỗng
            if (size === undefined) {
                size = '';
            }

            // ajax là công nghệ sử dụng js tương tác với server (database) mà không cần reload lại trang.
            // ajax của jquery:
            $.post('api/ajax_request.php', {
            'action': 'cart',
            'id': productId,
            'num': num,
            'size': size
            }, function(data) {
            location.reload()
            })
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Xử lý Quick View popup START -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-body p-0">
            <button type="button" class="close position-absolute" data-dismiss="modal" style="right: 15px; top: 10px; z-index: 999; font-size: 30px;">&times;</button>
            <div class="row no-gutters">
            <div class="col-md-6 d-flex align-items-center justify-content-center bg-light" style="border-radius: 3rem;">
                <img id="qv_thumbnail" src="" style="max-width: 100%; max-height: 400px; object-fit: contain">
            </div>
            
            <div class="col-md-6">
                <div class="p-4">
                <h4 id="qv_title" style="font-weight: 700; margin-bottom: 15px;">Tên sản phẩm</h4>
                
                <div class="mb-3">
                    <span id="qv_discount" style="color: red; font-size: 22px; font-weight: bold; margin-right: 10px;"></span>
                    <span id="qv_price" style="color: #888; text-decoration: line-through;"></span>
                </div>

                <div id="qv_size_area" class="mb-3">
                    <p style="font-weight: bold; margin-bottom: 5px;">Kích thước:</p>
                    <div id="qv_size_list" style="display: flex; gap: 10px;">
                    </div>
                    <p id="qv_size_warning" style="color: red; font-size: 12px; margin-top: 5px; display: none;">Vui lòng chọn kích thước!</p>
                </div>

                <div class="mb-4">
                    <p style="font-weight: bold; margin-bottom: 5px;">Số lượng:</p>
                    <div style="display: flex; align-items: center;">
                    <button class="btn btn-light border" onclick="updateQvQty(-1)">-</button>
                    <input type="number" id="qv_quantity" step="1" value="1" class="form-control border-top border-bottom" style="width: 60px; border-radius: 0; height: 38px; text-align: center;" readonly onchange="fixCartNum()">
                    <button class="btn btn-light border" onclick="updateQvQty(1)">+</button>
                    </div>
                </div>

                <button id="qv_btn_add" class="btn btn-dark btn-block btn-lg" style="font-weight: bold;">
                    Thêm vào giỏ
                </button>
                
                <div class="mt-3">
                    <a id="qv_link_detail" href="#" style="text-decoration: underline; font-size: 13px;">Xem chi tiết đầy đủ »</a>
                </div>

                <input type="hidden" id="qv_product_id">
                <input type="hidden" id="qv_selected_size">
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>

    <style>
    /* CSS cho nút size trong modal */
    .size-btn {
        border: 1px solid #ddd;
        padding: 5px 12px;
        cursor: pointer;
        background: #fff;
        min-width: 35px;
        text-align: center;
        transition: 0.2s;
    }
    .size-btn:hover {
        border-color: #000;
    }
    .size-btn.active {
        background: #000;
        color: #fff;
        border-color: #000;
    }
    </style>

    <script>
    // 1. Hàm hiển thị Popup Quick View
    function showQuickView(btn) {
        // Lấy dữ liệu từ nút bấm (data-attributes)
        var id = $(btn).data('id');
        var title = $(btn).data('title');
        var price = $(btn).data('price');
        var discount = $(btn).data('discount');
        var thumbnail = $(btn).data('thumbnail');
        var sizes = $(btn).data('sizes'); // jQuery tự parse JSON nếu đúng định dạng

        // Đổ dữ liệu vào Modal HTML
        $('#qv_product_id').val(id);
        $('#qv_title').text(title);
        $('#qv_price').text(price);
        $('#qv_discount').text(discount);
        $('#qv_thumbnail').attr('src', thumbnail);
        $('#qv_link_detail').attr('href', 'detail.php?id=' + id);
        
        // Reset trạng thái
        $('#qv_quantity').val(1);
        $('#qv_selected_size').val('');
        $('#qv_size_warning').hide();

        // Xử lý hiển thị danh sách Size
        var sizeHtml = '';
        if (sizes && sizes.length > 0) {
            $('#qv_size_area').show();
            sizes.forEach(function(size) {
                sizeHtml += `<span class="size-btn" onclick="selectQuickViewSize(this, '${size}')">${size}</span>`;
            });
        } else {
            $('#qv_size_area').hide();
        }
        $('#qv_size_list').html(sizeHtml);

        // Mở Modal
        $('#quickViewModal').modal('show');
    }

    // 2. Hàm chọn Size trong Modal
    function selectQuickViewSize(el, size) {
        $('#qv_size_list .size-btn').removeClass('active');
        $(el).addClass('active');
        $('#qv_selected_size').val(size);
        $('#qv_size_warning').hide();
    }

    // 3. Hàm tăng giảm số lượng
    function updateQvQty(delta) {
        var current = parseInt($('#qv_quantity').val());
        var newQty = current + delta;
        if (newQty < 1) newQty = 1;
        if (newQty > 999) newQty = 999;
        $('#qv_quantity').val(newQty);
    }

    // 4. Xử lý nút THÊM VÀO GIỎ trong Modal
    $('#qv_btn_add').click(function() {
        var id = $('#qv_product_id').val();
        var num = $('#qv_quantity').val();
        var size = $('#qv_selected_size').val();
        var hasSize = $('#qv_size_list').children().length > 0;

        // Validate: Nếu có size mà chưa chọn thì báo lỗi
        if (hasSize && size === '') {
            $('#qv_size_warning').show();
            return;
        }

        // Gọi hàm addCart (đã có sẵn trong footer.php của bạn)
        addCart(id, num, size);
        
        // Ẩn modal
        $('#quickViewModal').modal('hide');
    });

    function fixCartNum() {
        $('#qv_quantity').val(Math.abs($('#qv_quantity').val()));
    }
    </script>
    <!-- Xử lý Quick View popup STOP -->

    <div id="page-overlay"></div>
</body>
</html>
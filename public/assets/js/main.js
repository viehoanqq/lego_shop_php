document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('liveSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let typingTimer; // Biến để delay chống spam server (Debounce)

    if (searchInput && searchSuggestions) {
        // Khi người dùng gõ phím
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            const keyword = this.value.trim();

            if (keyword.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }

            // Đợi người dùng dừng gõ 300ms mới gửi Request
            typingTimer = setTimeout(() => {
                fetch(`/lego_shop_php/product/liveSearch?keyword=${encodeURIComponent(keyword)}`)
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        if (data.length > 0) {
                            // Duyệt qua mảng JSON để tạo HTML
                            data.forEach(item => {
                                const imgUrl = item.main_image ? `/lego_shop_php/public/assets/images/${item.main_image}` : '/lego_shop_php/public/assets/images/default-lego.jpg';
                                // Format giá tiền VNĐ
                                const price = new Intl.NumberFormat('vi-VN').format(item.selling_price) + 'đ';
                                
                                html += `
                                <a href="/lego_shop_php/product/detail/${item.id}" class="suggest-item">
                                    <img src="${imgUrl}" class="suggest-img" alt="${item.name}">
                                    <div class="suggest-info">
                                        <span class="suggest-name">${item.name}</span>
                                        <span class="suggest-price">${price}</span>
                                    </div>
                                </a>`;
                            });
                        } else {
                            html = '<div style="padding: 15px; text-align: center; color: #888; font-size: 14px;">Không tìm thấy sản phẩm nào phù hợp</div>';
                        }
                        
                        searchSuggestions.innerHTML = html;
                        searchSuggestions.style.display = 'block';
                    })
                    .catch(error => console.error('Lỗi Live Search:', error));
            }, 300);
        });

        // Bấm ra ngoài vùng tìm kiếm thì ẩn Dropdown đi
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });

        // Click lại vào ô input nếu có chữ thì hiện lại Dropdown
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && searchSuggestions.innerHTML !== '') {
                searchSuggestions.style.display = 'block';
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // 1. LIVE SEARCH (TÌM KIẾM NHANH)
    // ==========================================
    const searchInput = document.getElementById('liveSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let typingTimer;

    if (searchInput && searchSuggestions) {
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            const keyword = this.value.trim();

            if (keyword.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }

            typingTimer = setTimeout(() => {
                fetch(`/lego_shop_php/product/liveSearch?keyword=${encodeURIComponent(keyword)}`)
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const imgUrl = item.main_image ? `/lego_shop_php/public/assets/images/${item.main_image}` : '/lego_shop_php/public/assets/images/logo.png';
                                const price = new Intl.NumberFormat('vi-VN').format(item.selling_price) + 'đ';
                                html += `
                                <a href="/lego_shop_php/product/detail/${item.id}" class="suggest-item">
                                    <img src="${imgUrl}" class="suggest-img">
                                    <div class="suggest-info">
                                        <span class="suggest-name">${item.name}</span>
                                        <span class="suggest-price">${price}</span>
                                    </div>
                                </a>`;
                            });
                        } else {
                            html = '<div style="padding: 15px; text-align: center; color: #888;">Không tìm thấy sản phẩm</div>';
                        }
                        searchSuggestions.innerHTML = html;
                        searchSuggestions.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });
    }

    // ==========================================
    // 2. TÌM KIẾM NÂNG CAO (MODAL & SLIDER)
    // ==========================================
    const openBtn = document.getElementById("openAdvancedSearch");
    const closeBtn = document.getElementById("closeAdvancedSearch");
    const overlay = document.getElementById("advancedSearchOverlay");
    const form = document.getElementById("advancedSearchForm");

    // Đóng mở Popup
    if (openBtn && overlay) openBtn.onclick = () => overlay.classList.add("active");
    if (closeBtn && overlay) closeBtn.onclick = () => overlay.classList.remove("active");
    window.onclick = (e) => { if (e.target === overlay) overlay.classList.remove("active"); };

    // Xử lý Thanh kéo giá (Slider)
    const rangeMin = document.getElementById('rangeMin');
    const rangeMax = document.getElementById('rangeMax');
    const valMin = document.getElementById('priceMinValue');
    const valMax = document.getElementById('priceMaxValue');
    const highlight = document.getElementById('sliderHighlight');
    const priceGap = 500000;

    function updateSlider() {
        if (!rangeMin || !rangeMax || !valMin || !valMax) return;

        let minVal = parseInt(rangeMin.value);
        let maxVal = parseInt(rangeMax.value);

        if (maxVal - minVal < priceGap) {
            if (document.activeElement === rangeMin) {
                rangeMin.value = maxVal - priceGap;
                minVal = parseInt(rangeMin.value);
            } else {
                rangeMax.value = minVal + priceGap;
                maxVal = parseInt(rangeMax.value);
            }
        }

        valMin.innerText = new Intl.NumberFormat('vi-VN').format(minVal) + 'đ';
        valMax.innerText = new Intl.NumberFormat('vi-VN').format(maxVal) + 'đ';

        if (highlight) {
            const minP = (minVal / rangeMin.max) * 100;
            const maxP = (maxVal / rangeMax.max) * 100;
            highlight.style.left = minP + "%";
            highlight.style.width = (maxP - minP) + "%";
        }
    }

    if (rangeMin && rangeMax) {
        rangeMin.oninput = updateSlider;
        rangeMax.oninput = updateSlider;
        updateSlider(); // Chạy ngay khi load
    }

    // Submit Form nâng cao
    if (form) {
        form.onsubmit = function(e) {
            e.preventDefault();
            const keyword = document.getElementById("keyword")?.value || "";
            const category = document.getElementById("category")?.value || "";
            const age = document.getElementById("age")?.value || "";
            const pMin = rangeMin?.value || "0";
            const pMax = rangeMax?.value || "10000000";

            let url = `/lego_shop_php/product/filter?keyword=${encodeURIComponent(keyword)}&category=${category}&age=${encodeURIComponent(age)}&min_price=${pMin}&max_price=${pMax}`;
            window.location.href = url;
        };
    }
});
// ==========================================
// HÀM TẠO TOAST NOTIFICATION
// Sử dụng: showToast("Nội dung", "error" hoặc "success")
// ==========================================
function showToast(message, type = 'error') {
    // 1. Tìm hoặc tạo vùng chứa Toast
    let toastBox = document.getElementById('toastBox');
    if (!toastBox) {
        toastBox = document.createElement('div');
        toastBox.id = 'toastBox';
        document.body.appendChild(toastBox);
    }

    // 2. Tạo thẻ Toast
    let toast = document.createElement('div');
    toast.classList.add('custom-toast', type);
    
    // 3. Chọn Icon dựa theo type
    let icon = type === 'success' 
        ? '<i class="fa-solid fa-circle-check"></i>' 
        : '<i class="fa-solid fa-circle-exclamation"></i>';
        
    toast.innerHTML = icon + ' <span>' + message + '</span>';
    
    // 4. In ra màn hình
    toastBox.appendChild(toast);

    // 5. Kích hoạt hiệu ứng trượt vào (delay 10ms để CSS nhận diện)
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // 6. Tự động xóa sau 3 giây
    setTimeout(() => {
        toast.classList.remove('show'); // Trượt ra ngoài
        setTimeout(() => {
            toast.remove(); // Xóa hẳn khỏi DOM cho nhẹ máy
        }, 400); // Đợi trượt xong mới xóa
    }, 3000);
}
// ==========================================
// XỬ LÝ NÚT THÊM VÀO GIỎ HÀNG
// ==========================================
function addToCart(productId) {
    // 1. Kiểm tra trạng thái đăng nhập (Biến IS_LOGGED_IN được khai báo ở Header)
    if (typeof IS_LOGGED_IN !== 'undefined' && !IS_LOGGED_IN) {
        // Dùng Toast báo lỗi
        showToast("Vui lòng đăng nhập để sử dụng giỏ hàng!", "error");
        
        // Đợi 1.5 giây rồi chuyển về trang Login
        return; // Dừng hàm lại
    }

    // 2. Logic thêm vào giỏ (Bạn sẽ viết Ajax ở đây)
    // Tạm thời hiển thị Toast thành công để test:
    showToast("Đã thêm sản phẩm vào giỏ hàng!", "success");
    
    // Test thử xem ID sản phẩm có truyền vào đúng không
    console.log("Adding Product ID:", productId); 
}   
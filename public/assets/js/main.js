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
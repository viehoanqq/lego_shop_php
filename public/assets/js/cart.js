function formatVND(number) {
    return new Intl.NumberFormat('vi-VN').format(number) + 'đ';
}

function recalculateTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        let price = parseInt(item.querySelector('.item-price').getAttribute('data-price'));
        let qty = parseInt(item.querySelector('.input-qty').value);
        let subtotal = price * qty;
        item.querySelector('.item-subtotal').innerText = formatVND(subtotal);
        grandTotal += subtotal;
    });
    document.getElementById('summary-subtotal').innerText = formatVND(grandTotal);
    document.getElementById('summary-total').innerText = formatVND(grandTotal);
}

function updateCartQty(itemId, action) {
    const inputField = document.getElementById('qty-' + itemId);
    let currentQty = parseInt(inputField.value);
    if (action === 'decrease' && currentQty <= 1) return;

    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('action', action);

    fetch('/lego_shop_php/cart/updateQty', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            inputField.value = (action === 'increase') ? currentQty + 1 : currentQty - 1;
            recalculateTotal();
            // Optional: showToast("Đã cập nhật số lượng", "success");
        }
    });
}

function removeCartItem(itemId) {
    const formData = new FormData();
    formData.append('item_id', itemId);

    fetch('/lego_shop_php/cart/remove', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const itemRow = document.getElementById('item-' + itemId);
            itemRow.style.opacity = '0';
            
            // HIỆN TOAST Ở ĐÂY
            if (typeof showToast === "function") {
                showToast("Đã xóa sản phẩm khỏi giỏ hàng", "success");
            }

            setTimeout(() => {
                itemRow.remove();
                recalculateTotal();
                if(document.querySelectorAll('.cart-item').length === 0) window.location.reload();
            }, 300);
        } else {
            if (typeof showToast === "function") {
                showToast(data.msg || "Không thể xóa sản phẩm", "error");
            }
        }
    });
}
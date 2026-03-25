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
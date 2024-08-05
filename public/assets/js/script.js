
function changeImg(pic) {
    document.getElementById('change_image').src = pic;
}

//Lọc giá trị thấp cao
document.querySelector('#sort-form select[name="sort"]').addEventListener('change', function () {
    document.querySelector('#sort-form').submit();
});

document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-subcategories');

    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const subcategories = this.nextElementSibling; // Lấy phần tử <ul> tiếp theo
            const isExpanded = subcategories.classList.toggle('expanded'); // Mở hoặc đóng

            if (isExpanded) {
                // Khi mở, cập nhật nút thành '-'
                this.textContent = '-';
                subcategories.style.display = 'block'; // Hiện danh mục con
            } else {
                // Khi đóng, cập nhật nút thành '+'
                this.textContent = '+';
                subcategories.style.display = 'none'; // Ẩn danh mục con
            }
        });
    });

    // Ẩn nút "Quay lại" ban đầu

    // Chỉ hiển thị 5 danh mục cha đầu tiên
    // const parentCategoryItems = document.querySelectorAll('.parent-category');
    // parentCategoryItems.forEach(function (item, index) {
    //     if (index >= 5) {
    //         item.style.display = 'none';
    //     }
    // });

});
document.getElementById('seeAllButtonBrands').addEventListener('click', function () {
    const brandItems = document.querySelectorAll('.brand-item');
    brandItems.forEach(item => {
        item.style.display = 'block';
    });
    this.style.display = 'none'; // Ẩn nút "Xem Tất Cả"
    document.getElementById('backToTopBrands').style.display = 'block'; // Hiện nút "Quay lại"
});

document.getElementById('backToTopBrands').addEventListener('click', function () {
    const brandItems = document.querySelectorAll('.brand-item');
    brandItems.forEach((item, index) => {
        item.style.display = (index < 5) ? 'block' : 'none';
    });
    this.style.display = 'none'; // Ẩn nút "Quay lại"
    document.getElementById('seeAllButtonBrands').style.display = 'block'; // Hiện nút "Xem Tất Cả"
});
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-subcategories').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const categoryId = this.dataset.category;
            const subcategories = document.querySelector(`ul[data-parent-category='${categoryId}']`);
            if (subcategories.style.display === 'none' || subcategories.style.display === '') {
                subcategories.style.display = 'block';
                this.textContent = '-';
            } else {
                subcategories.style.display = 'none';
                this.textContent = '+';
            }
        });
    });
});

// Lọc theo danh mục
document.querySelectorAll('.category-link').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const selectedCategoryId = this.dataset.id;
        const url = new URL(window.location.href);

        // Cập nhật tham số category_id trong URL
        url.searchParams.set('category_id', selectedCategoryId);

        // Giữ lại giá nếu đã chọn
        const minPrice = url.searchParams.get('min_price');
        const maxPrice = url.searchParams.get('max_price');
        if (minPrice) {
            url.searchParams.set('min_price', minPrice);
        }
        if (maxPrice) {
            url.searchParams.set('max_price', maxPrice);
        }

        // Chuyển hướng đến URL mới
        window.location.href = url.toString();
    });
});

// Lọc theo thương hiệu
document.querySelectorAll('.brand-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const url = new URL(window.location.href);

        // Lấy tất cả các brand_ids đã chọn
        let brandIds = url.searchParams.getAll('brand_ids[]');

        // Nếu checkbox được chọn, thêm vào brandIds; nếu không, xóa khỏi brandIds
        if (this.checked) {
            brandIds.push(this.value);
        } else {
            brandIds = brandIds.filter(id => id !== this.value);
        }

        // Cập nhật tham số brand_ids trong URL
        url.searchParams.delete('brand_ids[]');
        brandIds.forEach(id => url.searchParams.append('brand_ids[]', id));

        // Giữ lại giá nếu đã chọn
        const minPrice = url.searchParams.get('min_price');
        const maxPrice = url.searchParams.get('max_price');
        if (minPrice) {
            url.searchParams.set('min_price', minPrice);
        }
        if (maxPrice) {
            url.searchParams.set('max_price', maxPrice);
        }

        // Chuyển hướng đến URL mới
        window.location.href = url.toString();
    });
});

// Lọc theo giá (giữ lại khoảng giá đã chọn khi thay đổi bộ lọc khác)
document.querySelectorAll('.price-button').forEach(button => {
    button.addEventListener('click', function () {
        const minPrice = this.getAttribute('data-min');
        const maxPrice = this.getAttribute('data-max');
        const url = new URL(window.location.href);

        // Cập nhật tham số URL cho giá
        url.searchParams.set('min_price', minPrice);
        if (maxPrice) {
            url.searchParams.set('max_price', maxPrice);
        } else {
            url.searchParams.delete('max_price');
        }

        // Chuyển hướng đến URL mới
        window.location.href = url.toString();
    });
});
//Lọc theo xếp hạng

document.addEventListener('DOMContentLoaded', function () {
    const ratingCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="star"]');
    const filterForm = document.getElementById('filterForm');

    ratingCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            // Khi checkbox được chọn hoặc bỏ chọn, gửi form
            filterForm.submit();
        });
    });

    filterForm.addEventListener('submit', function (event) {
        // Ngăn chặn hành động gửi form mặc định
        event.preventDefault();

        // Lưu trữ các xếp hạng đã chọn
        const selectedRatings = [];
        ratingCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedRatings.push(checkbox.value);
            }
        });

        // Xóa tất cả các input xếp hạng cũ
        const existingRatingInputs = filterForm.querySelectorAll('input[name="ratings[]"]');
        existingRatingInputs.forEach(input => input.remove());

        // Thêm các xếp hạng đã chọn dưới dạng input ẩn
        selectedRatings.forEach(rating => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'ratings[]';
            hiddenInput.value = rating;
            filterForm.appendChild(hiddenInput);
        });

        // Gửi form sau khi đã thêm input ẩn
        filterForm.submit();
    });
});


// Sắp xếp
document.querySelector('#sort-form select').addEventListener('change', function () {
    const selectedSort = this.value;
    const url = new URL(window.location.href);

    // Cập nhật tham số sort trong URL
    url.searchParams.set('sort', selectedSort);

    // Giữ lại tất cả các bộ lọc khác
    const minPrice = url.searchParams.get('min_price');
    const maxPrice = url.searchParams.get('max_price');
    const categoryId = url.searchParams.get('category_id');
    const brandIds = url.searchParams.getAll('brand_ids[]');

    if (minPrice) {
        url.searchParams.set('min_price', minPrice);
    }
    if (maxPrice) {
        url.searchParams.set('max_price', maxPrice);
    }
    if (categoryId) {
        url.searchParams.set('category_id', categoryId);
    }
    brandIds.forEach(id => {
        url.searchParams.append('brand_ids[]', id);
    });

    // Cập nhật giá trị selectedSort
    document.getElementById('selectedSort').value = selectedSort;

    // Chuyển hướng đến URL mới
    window.location.href = url.toString();
});

// Xử lý nút "Áp dụng" cho nhập giá
document.querySelector('.apply-button').addEventListener('click', function () {
    const minPrice = document.getElementById('minRangeInput').value;
    const maxPrice = document.getElementById('maxRangeInput').value;
    const url = new URL(window.location.href);

    // Cập nhật tham số URL cho giá
    if (minPrice) {
        url.searchParams.set('min_price', minPrice);
    } else {
        url.searchParams.delete('min_price');
    }

    if (maxPrice) {
        url.searchParams.set('max_price', maxPrice);
    } else {
        url.searchParams.delete('max_price');
    }

    // Chuyển hướng đến URL mới
    window.location.href = url.toString();
});


//Filler
document.addEventListener('DOMContentLoaded', function () {
    // Remove individual filters
    document.querySelectorAll('.remove-filter').forEach(button => {
        button.addEventListener('click', function () {
            let filterType = this.getAttribute('data-filter-type');
            let filterValue = this.getAttribute('data-filter-value');
            let url = new URL(window.location.href);

            if (filterType === 'category') {
                url.searchParams.delete('category_id');
            } else if (filterType === 'brand') {
                let brandIds = url.searchParams.getAll('brand_ids[]');
                brandIds = brandIds.filter(id => id !== filterValue);
                url.searchParams.delete('brand_ids[]');
                brandIds.forEach(id => url.searchParams.append('brand_ids[]', id));
            } else if (filterType === 'price') {
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
            } else if (filterType === 'rating') { // Logic for rating filter
                let ratings = url.searchParams.getAll('ratings[]');
                ratings = ratings.filter(rating => rating !== filterValue);
                url.searchParams.delete('ratings[]');
                ratings.forEach(rating => url.searchParams.append('ratings[]', rating));
            }

            window.location.href = url.toString();
        });
    });

    // Clear all filters
    document.getElementById('clearFilters').addEventListener('click', function () {
        let url = new URL(window.location.href);
        url.searchParams.delete('category_id');
        url.searchParams.delete('brand_ids[]');
        url.searchParams.delete('min_price');
        url.searchParams.delete('max_price');
        url.searchParams.delete('ratings[]'); // Xóa bộ lọc xếp hạng khi xóa tất cả
        window.location.href = url.toString();
    });
});







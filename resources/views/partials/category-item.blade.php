<li class="category-item {{ $level == 0 ? 'parent-category' : 'subcategory' }}" data-category="{{ $category->id }}"
    style="{{ $level < 5 ? '' : 'display:none;' }}">
    <div class="category-header">
        <a href="#" class="category-link" data-id="{{ $category->id }}">
            {{ $category->name }}
        </a>
        @if($category->children->isNotEmpty())
            <button type="button" class="toggle-subcategories" data-category="{{ $category->id }}">+</button>
        @endif
    </div>
    @if($category->children->isNotEmpty())
        <ul class="list-group subcategories" data-parent-category="{{ $category->id }}">
            @foreach($category->children as $subCategory)
                @include('partials.category-item', ['category' => $subCategory, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>

<style>
    .category-item {
        margin-bottom: 5px;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .category-header .category-link {
        flex-grow: 1;
    }

    .category-header .toggle-subcategories {
        background-color: white;
        color: blue;
        border: none;
        cursor: pointer;
        margin-left: 10px;
    }

    .subcategory {
        margin-left: 20px; /* Thụt lề các danh mục con */
    }

    .subcategories {
        display: none; /* Ẩn danh mục con ban đầu */
        transition: max-height 0.3s ease; /* Thêm chuyển động cho chiều cao */
    }

    .subcategories.expanded {
        display: block; /* Hiện danh mục con khi mở */
    }
</style>










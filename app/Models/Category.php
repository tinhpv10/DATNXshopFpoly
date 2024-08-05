<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'id',
        'name',
        'image',
        'category_id',
        'category_slug',
        'status',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'parent_id',
    ];
    use HasFactory;

    protected $casts = [
        'meta_keyword' => 'array',
    ];
    public function shop(): BelongsTo
    {
        return $this->BelongsTo(Shop::class);
    }

    public function categories(): HasMany
    {
        return $this->HasMany(Category::class, 'category_id');
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class, 'category_id');
    }

    public function post(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // cấp cha của danh mục
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    // lấy danh mục con
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // hàm lấy các cấp con của danh mục
    public static function categoryOptions()
    {
        $categories = self::whereNull('parent_id')->with('children')->get();
        $options = [];

        foreach ($categories as $category) {
            $options[$category->id] = $category->name;
            if ($category->children) {
                foreach ($category->children as $child) {
                    $options[$child->id] = '- ' . $child->name;
                }
            }
        }

        return $options;
    }

    // Accessor để lấy tên đầy đủ của danh mục bênh shop
    public function getFullNameAttribute()
    {
        $name = $this->name;
        $parent = $this->parent;

        while ($parent) {
            $name = $parent->name . ' -> ' . $name;
            $parent = $parent->parent;
        }

        return $name;
    }

    // Accessor để lấy tên đầy đủ với định dạng cấp bậc
    public function getIndentedNameAttribute()
    {
        $name = $this->name;
        $parent = $this->parent;
        $indentation = '';

        while ($parent) {
            $indentation = '- ' . $indentation;
            $parent = $parent->parent;
        }

        return $indentation . $name;
    }


    // Hàm để lấy danh mục cha và các cấp con của nó để hiển thị ra

    public function getIndentedChildrenNamesAttribute()
    {
        return $this->formatIndentedChildrenNames($this, '');
    }

    private function formatIndentedChildrenNames($category, $prefix)
    {
        $names = $prefix . $category->name;

        if ($category->children->isNotEmpty()) {
            foreach ($category->children as $child) {
                $names .= "\n" . $this->formatIndentedChildrenNames($child, $prefix . ' -> ');
            }
        }

        return $names;
    }

    // Phương thức để lấy cấp cha đầu tiên của danh mục
    public function getRootParent()
    {
        $category = $this;

        while ($category->parent) {
            $category = $category->parent;
        }

        return $category;
    }

    public function getAllDescendantIds()
    {
        $ids = [$this->id]; // Bắt đầu với ID của danh mục hiện tại

        foreach ($this->children as $child) {
            // Gọi đệ quy để lấy tất cả ID của danh mục con
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }

        return $ids;
    }

}

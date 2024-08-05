<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Thời Trang Nữ
        $fashionFemale = Category::create([
            'name' => 'Thời Trang Nữ',
            'category_slug' => 'thoi-nu-123456',
            'status' => 1,
            'meta_title' => 'Thời Trang Nữ',
            'meta_description' => 'Các sản phẩm thời trang dành cho nữ',
            'meta_keyword' => 'thời trang, nữ',
        ]);

        Category::create([
            'name' => 'Áo',
            'parent_id' => $fashionFemale->id,
            'category_slug' => 'ao-12345789',
            'status' => 1,
            'meta_title' => 'Áo Thời Trang Nữ',
            'meta_description' => 'Các loại áo thời trang dành cho nữ',
            'meta_keyword' => 'áo, thời trang, nữ',
        ]);

        Category::create([
            'name' => 'Quần',
            'parent_id' => $fashionFemale->id,
            'category_slug' => 'quan-127893',
            'status' => 1,
            'meta_title' => 'Quần Thời Trang Nữ',
            'meta_description' => 'Các loại quần thời trang dành cho nữ',
            'meta_keyword' => 'quần, thời trang, nữ',
        ]);

        Category::create([
            'name' => 'Váy',
            'parent_id' => $fashionFemale->id,
            'category_slug' => 'va23',
            'status' => 1,
            'meta_title' => 'Váy Thời Trang Nữ',
            'meta_description' => 'Các loại váy thời trang dành cho nữ',
            'meta_keyword' => 'váy, thời trang, nữ',
        ]);

        Category::create([
            'name' => 'Đầm',
            'parent_id' => $fashionFemale->id,
            'category_slug' => 'dam-123',
            'status' => 1,
            'meta_title' => 'Đầm Thời Trang Nữ',
            'meta_description' => 'Các loại đầm thời trang dành cho nữ',
            'meta_keyword' => 'đầm, thời trang, nữ',
        ]);

        Category::create([
            'name' => 'Đồ ngủ & đồ lót',
            'parent_id' => $fashionFemale->id,
            'category_slug' => 'do-ngu-do-lot-123',
            'status' => 1,
            'meta_title' => 'Đồ Ngủ & Đồ Lót Thời Trang Nữ',
            'meta_description' => 'Các loại đồ ngủ và đồ lót thời trang dành cho nữ',
            'meta_keyword' => 'đồ ngủ, đồ lót, thời trang, nữ',
        ]);

        // Thời Trang Nam
        $fashionMale = Category::create([
            'name' => 'Thời Trang Nam',
            'category_slug' => 'thoi-trang-nam-123',
            'status' => 1,
            'meta_title' => 'Thời Trang Nam',
            'meta_description' => 'Các sản phẩm thời trang dành cho nam',
            'meta_keyword' => 'thời trang, nam',
        ]);

        Category::create([
            'name' => 'Áo Nam',
            'parent_id' => $fashionMale->id,
            'category_slug' => 'ao-nam-123',
            'status' => 1,
            'meta_title' => 'Áo Thời Trang Nam',
            'meta_description' => 'Các loại áo thời trang dành cho nam',
            'meta_keyword' => 'áo, thời trang, nam',
        ]);

        Category::create([
            'name' => 'Quần Nam',
            'parent_id' => $fashionMale->id,
            'category_slug' => 'quan-nam-123',
            'status' => 1,
            'meta_title' => 'Quần Thời Trang Nam',
            'meta_description' => 'Các loại quần thời trang dành cho nam',
            'meta_keyword' => 'quần, thời trang, nam',
        ]);

        // Điện Thoại & Phụ Kiện
        $electronics = Category::create([
            'name' => 'Điện Thoại & Phụ Kiện',
            'category_slug' => 'dien-thoai-phu-kien-123',
            'status' => 1,
            'meta_title' => 'Điện Thoại & Phụ Kiện',
            'meta_description' => 'Các sản phẩm điện thoại và phụ kiện',
            'meta_keyword' => 'điện thoại, phụ kiện',
        ]);

        Category::create([
            'name' => 'Điện Thoại Di Động',
            'parent_id' => $electronics->id,
            'category_slug' => 'dien-thoai-di-dong-123',
            'status' => 1,
            'meta_title' => 'Điện Thoại Di Động',
            'meta_description' => 'Các loại điện thoại di động',
            'meta_keyword' => 'điện thoại, di động',
        ]);

        Category::create([
            'name' => 'Ốp Lưng & Miếng Dán',
            'parent_id' => $electronics->id,
            'category_slug' => 'op-lung-mieng-dan-123',
            'status' => 1,
            'meta_title' => 'Ốp Lưng & Miếng Dán',
            'meta_description' => 'Các loại ốp lưng và miếng dán',
            'meta_keyword' => 'ốp lưng, miếng dán',
        ]);

        Category::create([
            'name' => 'Tai Nghe',
            'parent_id' => $electronics->id,
            'category_slug' => 'tai-nghe-123',
            'status' => 1,
            'meta_title' => 'Tai Nghe',
            'meta_description' => 'Các loại tai nghe',
            'meta_keyword' => 'tai nghe, phụ kiện',
        ]);

        // Máy Tính & Laptop
        $computers = Category::create([
            'name' => 'Máy Tính & Laptop',
            'category_slug' => 'may-tinh-laptop-123',
            'status' => 1,
            'meta_title' => 'Máy Tính & Laptop',
            'meta_description' => 'Các sản phẩm máy tính và laptop',
            'meta_keyword' => 'máy tính, laptop',
        ]);

        Category::create([
            'name' => 'Laptop',
            'parent_id' => $computers->id,
            'category_slug' => 'laptop-123',
            'status' => 1,
            'meta_title' => 'Laptop',
            'meta_description' => 'Các loại laptop',
            'meta_keyword' => 'laptop, máy tính',
        ]);

        Category::create([
            'name' => 'Máy Tính Bàn',
            'parent_id' => $computers->id,
            'category_slug' => 'may-tinh-ban-123',
            'status' => 1,
            'meta_title' => 'Máy Tính Bàn',
            'meta_description' => 'Các loại máy tính bàn',
            'meta_keyword' => 'máy tính bàn, máy tính',
        ]);

        Category::create([
            'name' => 'Linh Kiện Máy Tính',
            'parent_id' => $computers->id,
            'category_slug' => 'linh-kien-may-tinh-123',
            'status' => 1,
            'meta_title' => 'Linh Kiện Máy Tính',
            'meta_description' => 'Các loại linh kiện máy tính',
            'meta_keyword' => 'linh kiện, máy tính',
        ]);

        // Nhà Cửa & Đời Sống
        $homeLiving = Category::create([
            'name' => 'Nhà Cửa & Đời Sống',
            'category_slug' => 'nha-cua-doi-song-123',
            'status' => 1,
            'meta_title' => 'Nhà Cửa & Đời Sống',
            'meta_description' => 'Các sản phẩm nhà cửa và đời sống',
            'meta_keyword' => 'nhà cửa, đời sống',
        ]);

        Category::create([
            'name' => 'Đồ Gia Dụng',
            'parent_id' => $homeLiving->id,
            'category_slug' => 'do-gia-dung-123',
            'status' => 1,
            'meta_title' => 'Đồ Gia Dụng',
            'meta_description' => 'Các loại đồ gia dụng',
            'meta_keyword' => 'đồ gia dụng, nhà cửa',
        ]);

        Category::create([
            'name' => 'Đồ Trang Trí Nhà Cửa',
            'parent_id' => $homeLiving->id,
            'category_slug' => 'do-trang-tri-nha-cua-123',
            'status' => 1,
            'meta_title' => 'Đồ Trang Trí Nhà Cửa',
            'meta_description' => 'Các loại đồ trang trí nhà cửa',
            'meta_keyword' => 'đồ trang trí, nhà cửa',
        ]);

        // Sức Khỏe & Sắc Đẹp
        $healthBeauty = Category::create([
            'name' => 'Sức Khỏe & Sắc Đẹp',
            'category_slug' => 'suc-khoe-sac-dep-123',
            'status' => 1,
            'meta_title' => 'Sức Khỏe & Sắc Đẹp',
            'meta_description' => 'Các sản phẩm sức khỏe và sắc đẹp',
            'meta_keyword' => 'sức khỏe, sắc đẹp',
        ]);

        Category::create([
            'name' => 'Mỹ Phẩm',
            'parent_id' => $healthBeauty->id,
            'category_slug' => 'my-pham-123',
            'status' => 1,
            'meta_title' => 'Mỹ Phẩm',
            'meta_description' => 'Các loại mỹ phẩm',
            'meta_keyword' => 'mỹ phẩm, sắc đẹp',
        ]);

        Category::create([
            'name' => 'Chăm Sóc Da',
            'parent_id' => $healthBeauty->id,
            'category_slug' => 'cham-soc-da-123',
            'status' => 1,
            'meta_title' => 'Chăm Sóc Da',
            'meta_description' => 'Các sản phẩm chăm sóc da',
            'meta_keyword' => 'chăm sóc da, sắc đẹp',
        ]);

        Category::create([
            'name' => 'Chăm Sóc Tóc',
            'parent_id' => $healthBeauty->id,
            'category_slug' => 'cham-soc-toc-123',
            'status' => 1,
            'meta_title' => 'Chăm Sóc Tóc',
            'meta_description' => 'Các sản phẩm chăm sóc tóc',
            'meta_keyword' => 'chăm sóc tóc, sắc đẹp',
        ]);

        // Thể Thao & Du Lịch
        $sportsTravel = Category::create([
            'name' => 'Thể Thao & Du Lịch',
            'category_slug' => 'the-thao-du-lich-123',
            'status' => 1,
            'meta_title' => 'Thể Thao & Du Lịch',
            'meta_description' => 'Các sản phẩm thể thao và du lịch',
            'meta_keyword' => 'thể thao, du lịch',
        ]);

        Category::create([
            'name' => 'Trang Phục Thể Thao',
            'parent_id' => $sportsTravel->id,
            'category_slug' => 'trang-phuc-the-thao-123',
            'status' => 1,
            'meta_title' => 'Trang Phục Thể Thao',
            'meta_description' => 'Các loại trang phục thể thao',
            'meta_keyword' => 'trang phục, thể thao',
        ]);

        Category::create([
            'name' => 'Dụng Cụ Thể Thao',
            'parent_id' => $sportsTravel->id,
            'category_slug' => 'dung-cu-the-thao-123',
            'status' => 1,
            'meta_title' => 'Dụng Cụ Thể Thao',
            'meta_description' => 'Các loại dụng cụ thể thao',
            'meta_keyword' => 'dụng cụ, thể thao',
        ]);

        Category::create([
            'name' => 'Thiết Bị Cắm Trại',
            'parent_id' => $sportsTravel->id,
            'category_slug' => 'thiet-bi-cam-trai-123',
            'status' => 1,
            'meta_title' => 'Thiết Bị Cắm Trại',
            'meta_description' => 'Các loại thiết bị cắm trại',
            'meta_keyword' => 'thiết bị, cắm trại',
        ]);

        // Mẹ & Bé
        $momBaby = Category::create([
            'name' => 'Mẹ & Bé',
            'category_slug' => 'me-be-123',
            'status' => 1,
            'meta_title' => 'Mẹ & Bé',
            'meta_description' => 'Các sản phẩm cho mẹ và bé',
            'meta_keyword' => 'mẹ, bé',
        ]);

        Category::create([
            'name' => 'Quần Áo Trẻ Em',
            'parent_id' => $momBaby->id,
            'category_slug' => 'quan-ao-tre-em-123',
            'status' => 1,
            'meta_title' => 'Quần Áo Trẻ Em',
            'meta_description' => 'Các loại quần áo trẻ em',
            'meta_keyword' => 'quần áo, trẻ em',
        ]);

        Category::create([
            'name' => 'Đồ Chơi Trẻ Em',
            'parent_id' => $momBaby->id,
            'category_slug' => 'do-choi-tre-em-123',
            'status' => 1,
            'meta_title' => 'Đồ Chơi Trẻ Em',
            'meta_description' => 'Các loại đồ chơi trẻ em',
            'meta_keyword' => 'đồ chơi, trẻ em',
        ]);

        Category::create([
            'name' => 'Sữa & Thực Phẩm Cho Bé',
            'parent_id' => $momBaby->id,
            'category_slug' => 'sua-thuc-pham-cho-be-123',
            'status' => 1,
            'meta_title' => 'Sữa & Thực Phẩm Cho Bé',
            'meta_description' => 'Các loại sữa và thực phẩm cho bé',
            'meta_keyword' => 'sữa, thực phẩm, bé',
        ]);

        // Thực Phẩm & Đồ Uống
        $foodDrink = Category::create([
            'name' => 'Thực Phẩm & Đồ Uống',
            'category_slug' => 'thuc-pham-do-uong-123',
            'status' => 1,
            'meta_title' => 'Thực Phẩm & Đồ Uống',
            'meta_description' => 'Các loại thực phẩm và đồ uống',
            'meta_keyword' => 'thực phẩm, đồ uống',
        ]);

        Category::create([
            'name' => 'Thực Phẩm Tươi Sống',
            'parent_id' => $foodDrink->id,
            'category_slug' => 'thuc-pham-tuoi-song-123',
            'status' => 1,
            'meta_title' => 'Thực Phẩm Tươi Sống',
            'meta_description' => 'Các loại thực phẩm tươi sống',
            'meta_keyword' => 'thực phẩm, tươi sống',
        ]);

        Category::create([
            'name' => 'Đồ Uống',
            'parent_id' => $foodDrink->id,
            'category_slug' => 'do-uong-123',
            'status' => 1,
            'meta_title' => 'Đồ Uống',
            'meta_description' => 'Các loại đồ uống',
            'meta_keyword' => 'đồ uống, thực phẩm',
        ]);

        Category::create([
            'name' => 'Thực Phẩm Chế Biến',
            'parent_id' => $foodDrink->id,
            'category_slug' => 'thuc-pham-che-bien-123',
            'status' => 1,
            'meta_title' => 'Thực Phẩm Chế Biến',
            'meta_description' => 'Các loại thực phẩm chế biến',
            'meta_keyword' => 'thực phẩm, chế biến',
        ]);

        // Sách, VPP & Quà Tặng
        $booksStationery = Category::create([
            'name' => 'Sách, VPP & Quà Tặng',
            'category_slug' => 'sach-vpp-qua-tang-123',
            'status' => 1,
            'meta_title' => 'Sách, VPP & Quà Tặng',
            'meta_description' => 'Các sản phẩm sách, văn phòng phẩm và quà tặng',
            'meta_keyword' => 'sách, văn phòng phẩm, quà tặng',
        ]);

        Category::create([
            'name' => 'Sách',
            'parent_id' => $booksStationery->id,
            'category_slug' => 'sach-123',
            'status' => 1,
            'meta_title' => 'Sách',
            'meta_description' => 'Các loại sách',
            'meta_keyword' => 'sách, quà tặng',
        ]);

        Category::create([
            'name' => 'Văn Phòng Phẩm',
            'parent_id' => $booksStationery->id,
            'category_slug' => 'van-phong-pham-123',
            'status' => 1,
            'meta_title' => 'Văn Phòng Phẩm',
            'meta_description' => 'Các loại văn phòng phẩm',
            'meta_keyword' => 'văn phòng phẩm, sách',
        ]);
    }
}

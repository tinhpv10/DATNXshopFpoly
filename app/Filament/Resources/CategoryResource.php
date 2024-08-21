<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Danh mục';
    protected static ?string $navigationGroup = 'Sản phẩm';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->label('Ảnh đại diện')
                    ->required()
                    ->columnSpan(2),
                Select::make('parent_id')
                    ->options(function () {
                        $categories = Category::whereNull('parent_id')->with('children')->get();
                        $options = [];

                        // Hàm đệ quy để lấy danh mục con
                        $addChildrenToOptions = function ($categories, $indentation = '') use (&$options, &$addChildrenToOptions) {
                            foreach ($categories as $category) {
                                // Thêm thụt lề vào danh mục
                                $options[$category->id] = $indentation . $category->name;
                                if ($category->children) {
                                    // Thêm một khoảng thụt lề cho các cấp con
                                    $newIndentation = $indentation . '_ '; // Thêm khoảng trắng để thụt lề
                                    $addChildrenToOptions($category->children, $newIndentation);
                                }
                            }
                        };

                        // Gọi hàm đệ quy với danh mục cấp 1
                        $addChildrenToOptions($categories);

                        return $options;
                    })

                    ->searchable()
                    ->label('Thuộc danh mục'),
                TextInput::make('name')
                    ->maxLength(70)
                    ->label('Danh mục')
                    ->required(),
                TextInput::make('category_slug')
                    ->label('Slug Danh mục')
                    ->unique()
                    ->validationMessages([
                        'unique' => 'Slug này đã được thêm rồi',
                    ])
                    ->required(),
                TextInput::make('meta_title')
                    ->label('Tiêu đề SEO')
                    ->maxLength(100)
                    ->required(),
                TagsInput::make('meta_keyword')
                    ->label('Từ khóa SEO')
                    ->required(),
                MarkdownEditor::make('meta_description')
                    ->label('Mô tả SEO')
                    ->required()
                    ->columnSpan(2),
                Toggle::make('status')
                    ->label('Trạng thái')
                    ->inline(false),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {

        return $infolist
            ->schema([
                TextEntry::make('image')
                    ->label('Ảnh đại diện'),
                TextEntry::make('indented_children_names')
                    ->label('Danh mục')
                    ->formatStateUsing(function ($state, $record) {
                        return nl2br(e($record->indented_children_names));
                    })
                    ->html(), // Cho phép hiển thị HTML
                TextEntry::make('category_slug')
                    ->label('Slug Danh mục'),
                TextEntry::make('meta_title')
                    ->label('Tiêu đề SEO'),
                TextEntry::make('meta_description')
                    ->label('Từ khóa SEO'),
                TextEntry::make('meta_keyword')
                    ->label('Từ khóa SEO'),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->query(Category::query()->whereNull('parent_id')->with('children'))
//            ->query(Category::query()->whereNull('parent_id')->with('children'))
            ->defaultGroup('name')
            ->columns([
//                ImageColumn::make('image')
//                    ->width(100)
//                    ->height(100)
//                    ->label('Ảnh đại diện'),
                TextColumn::make('indented_name')
                    ->label('Danh mục')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->indented_name;
                    }),
                ToggleColumn::make('status')
                    ->label('Trạng thái'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

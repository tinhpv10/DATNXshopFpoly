<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\CategoryPost;
use App\Models\Post;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Section as SectionInfolist;
use Filament\Infolists\Components\ImageEntry;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Bài viết';
    protected static ?string $navigationGroup = 'Bài viết';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->required()
                                    ->label('Ảnh đại diện'),

                            ]),

                        Section::make()
                            ->schema([
                                Select::make('category_post_id')
                                    ->relationship(name: 'CategoryPost', titleAttribute: 'name')
                                    ->required()
                                    ->options(CategoryPost::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->label('Danh mục'),

                                TextInput::make('title')
                                    ->label('Tiêu đề bài viết')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Set $set, Get $get) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                        $set('meta_title', $state);

                                        if (empty($get('meta_title'))) {
                                            $question_key = 'Đề xuất các từ khóa SEO chính cho chủ đề ';
                                            $question_content = 'Tạo một outline chi tiết cho một bài blog ' . $get('title') . '. Phong cách viết nên thân thiện, dễ hiểu. Bài viết cần có độ dài khoảng 1000 từ.';

                                            $answer_key = PostResource::disguise_curl($question_key . $get('title'));
                                            $answer_content = PostResource::disguise_curl($question_content);

                                            $set('SEO', $answer_key . $answer_content);

                                        } else {
                                            $question_key = 'Đề xuất các từ khóa SEO chính cho chủ đề ';
                                            $question_content = 'Tạo một outline chi tiết cho một bài blog' . $get('meta_title') . 'Phong cách viết nên thân thiện, dễ hiểu. Bài viết cần có độ dài khoảng 1000 từ.';

                                            $answer_key = PostResource::disguise_curl($question_key . $get('meta_title'));
                                            $answer_content = PostResource::disguise_curl($question_content);

                                            $set('SEO', $answer_key . $answer_content);
                                        }

                                    }),

                                TagsInput::make('tags')
                                    ->label('Nhãn bài viết')
                                    ->required(),

                                TextInput::make('slug')
                                    ->label('Đường dẫn bài viết')
                                    ->readOnly()
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Đường dẫn đã tồn tại.',
                                    ]),

                                RichEditor::make('content')
                                    ->label('Nội dung')
                                    ->required()
                                    ->columnSpan('full'),

                            ])->columns(2),
                    ])->columnSpan(2),

                Group::make()
                    ->schema([
                        Section::make('Nội dung SEO bài viết')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Tiêu đề SEO')
                                    ->maxLength(60)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if (empty($get('meta_title'))) {
                                            $question_key = 'Đề xuất các từ khóa SEO chính cho chủ đề ';
                                            $question_content = 'Tạo một outline chi tiết cho một bài blog' . $get('title') . 'Phong cách viết nên thân thiện, dễ hiểu. Bài viết cần có độ dài khoảng 1000 từ.';

                                            $answer_key = PostResource::disguise_curl($question_key . $get('title'));
                                            $answer_content = PostResource::disguise_curl($question_content);

                                            $set('SEO', $answer_key . $answer_content);

                                        } else {
                                            $question_key = 'Đề xuất các từ khóa SEO chính cho chủ đề ';
                                            $question_content = 'Tạo một outline chi tiết cho một bài blog' . $get('meta_title') . 'Phong cách viết nên thân thiện, dễ hiểu. Bài viết cần có độ dài khoảng 1000 từ.';

                                            $answer_key = PostResource::disguise_curl($question_key . $get('meta_title'));
                                            $answer_content = PostResource::disguise_curl($question_content);

                                            $set('SEO', $answer_key . $answer_content);
                                        }
                                    }),

                                TagsInput::make('meta_keyword')
                                    ->label('Từ khóa SEO')
                                    ->required(),

                                Textarea::make('meta_description')
                                    ->label('Mô tả SEO')
                                    ->required()
                                    ->rows(5)
                                    ->maxLength(155),

                                Textarea::make('SEO')
                                    ->autosize()
                                    ->rows(10)
                                    ->readOnly()
                                    ->label('Gợi ý nội dung bài viết'),

                            ]),
                    ])->columnSpan(1),

            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                SectionInfolist::make()
                    ->schema([
                        TextEntry::make('meta_title')
                            ->label('Tiêu đề SEO'),

                        TextEntry::make('meta_keyword')
                            ->label('Từ khóa SEO'),

                        TextEntry::make('meta_description')
                            ->label('Mô tả SEO')
                            ->columnSpan('full')
                    ])->columns(2),

                SectionInfolist::make()
                    ->schema([
                        TextEntry::make('User.name')
                            ->label('Người đăng bài'),

                        ImageEntry::make('thumbnail')
                            ->label('Hình đại diện'),

                        TextEntry::make('tags')
                            ->label('Nhãn bài viết'),

                        TextEntry::make('slug')
                            ->label('Đường dẫn bài viết'),

                        TextEntry::make('CategoryPost.name')
                            ->label('Danh mục bài viết'),

                        TextEntry::make('title')
                            ->label('Tiêu đề bài viết'),

                        TextEntry::make('content')
                            ->label('Nội dung bài viết')
                            ->html()
                            ->columnSpan('full'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Hình ảnh')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Tiêu đề bài viết')
                    ->searchable(),
                TextColumn::make('CategoryPost.name')
                    ->label('Danh mục bài viết')
                    ->searchable(),
                TextColumn::make('User.name')
                    ->label('Người đăng bài')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => Pages\ViewPost::route('/{record}'),

        ];
    }

    protected static function disguise_curl($content)
    {
        $curl = curl_init();
        $API_key = env('AI_KEY');
        $header = [
            "Content-Type: application/json",
            "x-goog-api-key: $API_key"
        ];

        $data = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $content
                        ]
                    ]
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent',
            CURLOPT_USERAGENT => 'Viblo Example POST',
            CURLOPT_POST => 1,
            CURLOPT_SSL_VERIFYPEER => false, // Bỏ kiểm tra SSL
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => json_encode($data), // Sử dụng json_encode thay vì http_build_query
        ]);

        $resp = curl_exec($curl);

        // Kiểm tra lỗi cURL
        if (curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
        }

        curl_close($curl);
        // Chuyển đổi phản hồi JSON thành mảng PHP
        $responseArray = json_decode($resp, true);
//        dd($responseArray);

        // Kiểm tra xem phản hồi có chứa dữ liệu hợp lệ không
        if (isset($responseArray['candidates']) && count($responseArray['candidates']) > 0) {
            $result = $responseArray['candidates'][0]['content']['parts'][0]['text'];
            return $result;
        } else {
            return 'No response from API or invalid response structure.';
        }

    }
}

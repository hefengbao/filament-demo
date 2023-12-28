<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('标题') // 标签，不设置的话则显示 Title
                    ->required() // 必填项
                    ->maxLength(100) // 验证规则，最大长度为 100
                    ->columnSpanFull() // 占满一行，默认是 1/2
                    ->live()
                    ->afterStateUpdated(fn(?string $state, Forms\Set $set) => $set('slug', Str::slug($state, '-', 'zh'))),
                Forms\Components\TextInput::make('slug')
                    ->columnSpanFull(),
                Forms\Components\MarkdownEditor::make('content') // 使用 Markdown 编辑器
                    ->label('内容')
                    ->required()
                    ->columnSpanFull(),
                //Forms\Components\RichEditor::make('content'), 富文本编辑器
                Forms\Components\FileUpload::make('image')
                    ->label('封面')
                    ->disk('public') // app/config/filesystems.php 中定义的 disks
                    ->directory('attachment/images') // 保存目录
                    ->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->label('分类')
                    ->relationship('category','name')
                    // relationship：
                    // 第一个参数 category 是在 app/Models/Post.php 中定义的 `category()` 关系,
                    // 第二个参数 name 表示用 Category 中的 name 的值作为选择框（select）选项（option）的内容
                    ->required()
                    ->preload() //预加载
                    ->searchable() //可搜索
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('名称')
                            ->required()
                    ]),
                Forms\Components\TagsInput::make('tags')
                    // 这里的 tags 对应 app/Models/Post.php 中定义的 `tags()` 关系,并且在 `$casts` 属性中转为 array
                    ->label('标签'),
                Forms\Components\Select::make('user_id')
                    ->label('作者')
                    ->relationship('author', 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('状态')
                    ->options([
                        'draft' => '草稿',
                        'publish' => '发布',
                    ])
                    ->default('draft') // 默认已选项
                    ->selectablePlaceholder(false) // 不显示请选择（Select an option）选项
                    ->live(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('发布时间')
                    ->disabled(fn(Forms\Get $get): bool => $get('status') !== 'publish'), // 仅当 status 的值为 publish 时才可用
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
        ];
    }
}

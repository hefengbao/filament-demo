<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Components\Tab;
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
                Forms\Components\Select::make('tags')
                    // 这里的 tags 对应 app/Models/Post.php 中定义的 `tags()` 关系,并且在 `$casts` 属性中转为 array
                    ->label('标签')
                    ->multiple() // 可以多选
                    ->placeholder('') // 重置 placeholder
                    ->relationship('tags','name')
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('名称')
                            ->required()
                    ]),
                Forms\Components\Select::make('user_id')
                    ->label('作者')
                    ->relationship('author', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->label('状态')
                    /*->options([
                        'draft' => '草稿',
                        'publish' => '发布',
                    ])*/
                    /*->options([
                        Status::Draft->value => '草稿',
                        Status::Publish->value => '发布',
                    ])*/
                    ->options(Status::class)
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
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->description(fn(Post $record) => $record->slug),//利用这个特性可以把字段合并显示
                Tables\Columns\TextColumn::make('author.name')
                    ->label('作者'),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge() // 显示为 badge
                    /*->color(fn(string $state): string => match($state){
                        'publish' => 'info',
                        default => 'primary'
                    })*/ // 定制 badge 颜色
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        'draft' => '草稿',
                        'publish' => '发布',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // 查看按钮
                Tables\Actions\EditAction::make(),// 编辑按钮，默认显示在每行的右侧，
                Tables\Actions\Action::make('pinned') // 名称，要唯一
                    ->label('置顶') // 显示的名称（标签）
                    ->color('danger') // 颜色
                    ->requiresConfirmation() // 显示确认框
                    ->action(function (){}) // 操作逻辑
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([ // 批量操作选项
                    Tables\Actions\DeleteBulkAction::make(),// 批量删除
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('title')
                ->label('标题'),
            TextEntry::make('slug')
                ->label('Slug'),
            TextEntry::make('author.name')
                ->label('作者'),
            TextEntry::make('status')
                ->label('状态')
                ->badge(), // 显示为 badge
            TextEntry::make('content')
                ->label('内容')
                ->markdown() // 解析 markdown
                ->columnSpanFull(), //占用整行
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
            'view' => Pages\ViewPage::route('{record}'), // 查看界面
        ];
    }
}

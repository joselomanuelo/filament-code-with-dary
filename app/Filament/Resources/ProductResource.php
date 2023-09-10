<?php

namespace App\Filament\Resources;

use App\Enums\ProductTypeEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Group::make()->schema([
                Section::make()->schema([
                    TextInput::make('name'),
                    TextInput::make('slug'),
                    MarkdownEditor::make('description')->columnSpan('full'),
                ])->columns(2),
                Section::make('Pricing and Inventory')->schema([
                    TextInput::make('sku'),
                    TextInput::make('price'),
                    TextInput::make('quantity'),
                    Select::make('type')->options([
                        'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                        'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                    ]),
                ])->columns(2),
            ]),
            Group::make()->schema([
                Section::make('Status')->schema([
                    Toggle::make('is_visible'),
                    Toggle::make('is_featured'),
                    DatePicker::make('published_at'),
                ]),
                Section::make('Image')->schema([
                    FileUpload::make('image'),
                ])->collapsible(),
                Section::make('Associations')->schema([
                    Select::make('brand_id')->relationship('brand', 'name'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name'),
                TextColumn::make('brand.name'),
                IconColumn::make('is_visible')->boolean(),
                TextColumn::make('price'),
                TextColumn::make('quantity'),
                TextColumn::make('published_at'),
                TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

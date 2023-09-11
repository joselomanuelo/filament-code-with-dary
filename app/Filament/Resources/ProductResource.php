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
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, string $state, Set $set) {
                            if ($operation !== 'create') {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->disabled()
                        ->dehydrated(),

                    MarkdownEditor::make('description')->columnSpan('full'),
                ])->columns(2),

                Section::make('Pricing and Inventory')->schema([
                    TextInput::make('sku')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->label('SKU (Stock Keeping Unit)'),

                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->rules('regex:/^\d{1,10}(\.\d{0,2})?$/'),

                    TextInput::make('quantity')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(2000),

                    Select::make('type')->options([
                        'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                        'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                    ])->required(),
                ])->columns(2),
            ]),

            Group::make()->schema([
                Section::make('Status')->schema([
                    Toggle::make('is_visible')
                        ->label('Visibility')
                        ->helperText('Enable o disable product visibility')
                        ->default(true),

                    Toggle::make('is_featured')
                        ->label('Featured')
                        ->helperText('Enable o disable product featured state')
                        ->default(true),

                    DatePicker::make('published_at')
                        ->label('Availability')
                        ->default(now()),
                ]),

                Section::make('Image')->schema([
                    FileUpload::make('image')
                        ->directory('product-attachments')
                        ->preserveFilenames()
                        ->image()
                        ->imageEditor(),
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

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('price')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->date()
                    ->sortable(),

                TextColumn::make('type'),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->trueLabel('Only visible products')
                    ->falseLabel('Only hidden products')
                    ->native(false),

                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
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

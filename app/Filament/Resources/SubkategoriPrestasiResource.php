<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubkategoriPrestasiResource\Pages;
use App\Filament\Resources\SubkategoriPrestasiResource\RelationManagers;
use App\Models\SubkategoriPrestasi;
use App\Models\KategoriPrestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubkategoriPrestasiResource extends Resource
{
    protected static ?string $model = SubkategoriPrestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Aset';

    protected static ?string $navigationLabel = 'Subkategori Prestasi';

    protected static ?string $slug = 'subkategori-prestasi';
    
    protected static ?string $label = 'Subkategori Prestasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_kategori_prestasi')
                ->relationship('kategoriPrestasi', 'kategori')
                ->required()
                ->searchable()
                ->preload()
                ->label('Kategori Prestasi')
                ->placeholder('Pilih Kategori Prestasi'),
                TextInput::make('subkategori')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kategoriPrestasi.kategori')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->label('Kategori Prestasi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subkategori')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->sortable()
                    ->searchable()
            ])
            ->filters([
                SelectFilter::make('id_kategori_prestasi')
                    ->relationship('kategoriPrestasi', 'kategori')
                    ->label('Kategori Prestasi')
                    ->placeholder('Pilih Kategori Prestasi')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpan([
                        'sm' => 2,
                        'lg' => 2,
                        'xl' => 2,
                        '2xl' => 2,
                    ])->default(null)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSubkategoriPrestasis::route('/'),
            'create' => Pages\CreateSubkategoriPrestasi::route('/create'),
            'edit' => Pages\EditSubkategoriPrestasi::route('/{record}/edit'),
        ];
    }
}

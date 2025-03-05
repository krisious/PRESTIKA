<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriPrestasiResource\Pages;
use App\Filament\Resources\KategoriPrestasiResource\RelationManagers;
use App\Models\KategoriPrestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriPrestasiResource extends Resource
{
    protected static ?string $model = KategoriPrestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kategori Prestasi';

    protected static ?string $slug = 'kategori-prestasi';
    
    protected static ?string $label = 'Kategori Prestasi';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kategori_prestasi')
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kategori_prestasi')
                ->copyable()
                ->copyMessage('Copy to Clipboard')
                ->searchable()
                ->sortable()
            ])
            ->filters([
                //
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
            'index' => Pages\ListKategoriPrestasis::route('/'),
            'create' => Pages\CreateKategoriPrestasi::route('/create'),
            'edit' => Pages\EditKategoriPrestasi::route('/{record}/edit'),
        ];
    }
}

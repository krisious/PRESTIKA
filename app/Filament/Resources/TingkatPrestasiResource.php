<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TingkatPrestasiResource\Pages;
use App\Filament\Resources\TingkatPrestasiResource\RelationManagers;
use App\Models\TingkatPrestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TingkatPrestasiResource extends Resource
{
    protected static ?string $model = TingkatPrestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Aset';

    protected static ?string $navigationLabel = 'Tingkat Prestasi';

    protected static ?string $slug = 'tingkat-prestasi';
    
    protected static ?string $label = 'Tingkat Prestasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tingkat')
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tingkat')
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
            'index' => Pages\ListTingkatPrestasis::route('/'),
            'create' => Pages\CreateTingkatPrestasi::route('/create'),
            'edit' => Pages\EditTingkatPrestasi::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeringkatPrestasiResource\Pages;
use App\Filament\Resources\PeringkatPrestasiResource\RelationManagers;
use App\Models\PeringkatPrestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeringkatPrestasiResource extends Resource
{
    protected static ?string $model = PeringkatPrestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Aset';
    
    protected static ?string $navigationLabel = 'Peringkat Prestasi';

    protected static ?string $slug = 'peringkat-prestasi';
    
    protected static ?string $label = 'peringkat Prestasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('peringkat')
                ->required()
                ->label('Peringkat')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peringkat')
                ->label('Peringkat')
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
            'index' => Pages\ListPeringkatPrestasis::route('/'),
            'create' => Pages\CreatePeringkatPrestasi::route('/create'),
            'edit' => Pages\EditPeringkatPrestasi::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DelegasiResource\Pages;
use App\Filament\Resources\DelegasiResource\RelationManagers;
use App\Models\Delegasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DelegasiResource extends Resource
{
    protected static ?string $model = Delegasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Aset';

    protected static ?string $navigationLabel = 'Delegasi';

    protected static ?string $slug = 'delegasi';
    
    protected static ?string $label = 'Delegasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('delegasi')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('delegasi')
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
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListDelegasis::route('/'),
            'create' => Pages\CreateDelegasi::route('/create'),
            'edit' => Pages\EditDelegasi::route('/{record}/edit'),
        ];
    }
}

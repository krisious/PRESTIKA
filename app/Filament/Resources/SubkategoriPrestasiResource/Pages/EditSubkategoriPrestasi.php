<?php

namespace App\Filament\Resources\SubkategoriPrestasiResource\Pages;

use App\Filament\Resources\SubkategoriPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubkategoriPrestasi extends EditRecord
{
    protected static string $resource = SubkategoriPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

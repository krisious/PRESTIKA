<?php

namespace App\Filament\Resources\PeringkatPrestasiResource\Pages;

use App\Filament\Resources\PeringkatPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeringkatPrestasi extends EditRecord
{
    protected static string $resource = PeringkatPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

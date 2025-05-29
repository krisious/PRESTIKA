<?php

namespace App\Filament\Resources\TingkatPrestasiResource\Pages;

use App\Filament\Resources\TingkatPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTingkatPrestasi extends EditRecord
{
    protected static string $resource = TingkatPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

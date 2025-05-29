<?php

namespace App\Filament\Resources\DelegasiResource\Pages;

use App\Filament\Resources\DelegasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDelegasi extends EditRecord
{
    protected static string $resource = DelegasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

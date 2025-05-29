<?php

namespace App\Filament\Resources\DelegasiResource\Pages;

use App\Filament\Resources\DelegasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDelegasis extends ListRecords
{
    protected static string $resource = DelegasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

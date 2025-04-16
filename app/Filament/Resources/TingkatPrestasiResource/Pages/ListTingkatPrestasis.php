<?php

namespace App\Filament\Resources\TingkatPrestasiResource\Pages;

use App\Filament\Resources\TingkatPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTingkatPrestasis extends ListRecords
{
    protected static string $resource = TingkatPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\KategoriPrestasiResource\Pages;

use App\Filament\Resources\KategoriPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPrestasis extends ListRecords
{
    protected static string $resource = KategoriPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

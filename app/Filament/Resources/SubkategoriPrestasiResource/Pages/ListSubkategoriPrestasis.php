<?php

namespace App\Filament\Resources\SubkategoriPrestasiResource\Pages;

use App\Filament\Resources\SubkategoriPrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubkategoriPrestasis extends ListRecords
{
    protected static string $resource = SubkategoriPrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

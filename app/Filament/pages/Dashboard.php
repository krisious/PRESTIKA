<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Tanggal Awal')
                            ->placeholder('Tanggal awal')   
                            ->columnspan(1),

                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->placeholder('Tanggal akhir')
                            ->columnspan(1),
                    ]),
            ])->columns(2);
    }
}

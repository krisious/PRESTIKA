<?php

namespace App\Filament\Pages;

use App\Models\TahunAjaran;
use App\Models\Jurusan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Widgets\AccountWidget;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
        ];
    }

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

                        Select::make('jurusan_id')
                            ->label('Jurusan')
                            ->options(Jurusan::pluck('jurusan', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih jurusan')
                            ->columnSpan(1),

                        Select::make('tahun_ajaran_id')
                            ->label('Tahun Ajaran')
                            ->options(TahunAjaran::orderByDesc('tahun')->pluck('tahun', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih tahun ajaran')
                            ->columnSpan(1),
                    ]),
            ])->columns(2);
    }

    public function updatedFilters(): void
    {
        $start = $this->filters['start_date'] ?? null;
        $end   = $this->filters['end_date'] ?? null;

        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate   = Carbon::parse($end);

            if ($startDate->diffInYears($endDate) > 3) {
                // Bersihkan filter agar tidak diterapkan
                $this->filters['start_date'] = null;
                $this->filters['end_date'] = null;

                Notification::make()
                    ->title('Rentang Melebihi Batas')
                    ->body('Melebihi batas rekap yakni 3 tahun.')
                    ->warning()
                    ->persistent()
                    ->send();
            }
        }
    }
}

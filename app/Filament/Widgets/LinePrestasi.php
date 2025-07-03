<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Prestasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LinePrestasi extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Grafik Trend Perkembangan Prestasi';
    
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Ambil tanggal dari filter, fallback jika tidak ada
        $startDate = $this->filters['start_date'] ?? now()->subMonths(5)->startOfMonth()->toDateString();
        $endDate = $this->filters['end_date'] ?? now()->endOfMonth()->toDateString();
        $jurusanId      = $this->filters['jurusan_id']      ?? null;
        $tahunAjaranId  = $this->filters['tahun_ajaran_id'] ?? null;

        if (!empty($startDate) && !empty($endDate)) {
            $startDate = Carbon::parse($startDate)->startOfMonth();
            $endDate = Carbon::parse($endDate)->endOfMonth();
        } else {
            // Default: 6 bulan terakhir
            $startDate = now()->subMonths(5)->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $months = collect();
        for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
            $months->push($date->format('M Y'));
        }
        
        // Ambil semua subkategori
        $subkategoris = Prestasi::join('subkategori_prestasis', 'prestasis.id_subkategori_prestasi', '=', 'subkategori_prestasis.id')
            ->select('subkategori_prestasis.id', 'subkategori_prestasis.subkategori')
            ->where('prestasis.status', 'diterima')
            ->distinct()
            ->get();

        $datasets = [];

        $colors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(201, 203, 207, 1)',
        ];

        $colorIndex = 0;

        foreach ($subkategoris as $subkategori) {
            $data = [];

            foreach ($months as $monthLabel) {
                $monthDate = Carbon::createFromFormat('M Y', $monthLabel);

                $query = Prestasi::where('id_subkategori_prestasi', $subkategori->id)
                    ->where('status', 'diterima')
                    ->whereDate('tanggal_perolehan', '>=', $startDate)
                    ->whereDate('tanggal_perolehan', '<=', $endDate)
                    ->whereYear('tanggal_perolehan', $monthDate->year)
                    ->whereMonth('tanggal_perolehan', $monthDate->month);

                if ($jurusanId) {
                    $query->whereHas('siswa', function ($q) use ($jurusanId) {
                        $q->where('id_jurusan', $jurusanId);
                    });
                }

                if ($tahunAjaranId) {
                    $query->where('id_tahun_ajaran', $tahunAjaranId);
                }

                $data[] = $query->count();
            }

            $color = $colors[$colorIndex % count($colors)];
            $colorIndex++;

            $datasets[] = [
                'label' => $subkategori->subkategori,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'fill' => false,
                'tension' => 0.4,
            ];
        }

        return [
            'labels' => $months,
            'datasets' => $datasets,
        ];
    }

    protected function getOptions(): ?array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

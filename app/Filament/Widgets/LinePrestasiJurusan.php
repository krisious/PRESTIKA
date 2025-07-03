<?php

namespace App\Filament\Widgets;

use App\Models\Prestasi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LinePrestasiJurusan extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Pertumbuhan Prestasi Siswa per Jurusan';
    protected static ?int $sort = 5;     
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $startDate      = $this->filters['start_date']      ?? now()->subMonths(5)->startOfMonth()->toDateString();
        $endDate        = $this->filters['end_date']        ?? now()->endOfMonth()->toDateString();
        $jurusanId      = $this->filters['jurusan_id']      ?? null;
        $tahunAjaranId  = $this->filters['tahun_ajaran_id'] ?? null;

        $startDate = Carbon::parse($startDate)->startOfMonth();
        $endDate   = Carbon::parse($endDate)->endOfMonth();

        $months = collect();
        for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
            $months->push($date->format('M Y'));
        }

        $prestasis = Prestasi::with([
                'siswa.jurusan',
                'anggotaTim.jurusan',
            ])
            ->where('status', 'diterima')
            ->whereBetween('tanggal_perolehan', [$startDate, $endDate])
            // 📅 Filter tahun ajaran
            ->when($tahunAjaranId, fn ($q) =>
                $q->where('id_tahun_ajaran', $tahunAjaranId)
            )
            ->when($jurusanId, fn ($q) => 
                $q->where(function ($qq) use ($jurusanId) {
                    $qq->whereHas('siswa', fn ($q2) => $q2->where('id_jurusan', $jurusanId))
                    ->orWhereHas('anggotaTim', fn ($q2) => $q2->where('id_jurusan', $jurusanId));
                })
            )
            ->get();

        $counters = [];

        foreach ($prestasis as $prestasi) {
            $monthLabel = Carbon::parse($prestasi->tanggal_perolehan)->format('M Y');

            // Siswa pelapor
            if ($prestasi->siswa?->jurusan) {
                $jurName = $prestasi->siswa->jurusan->jurusan;
                $counters[$jurName][$monthLabel] = ($counters[$jurName][$monthLabel] ?? 0) + 1;
            }

            // Anggota tim
            foreach ($prestasi->anggotaTim as $anggota) {
                if ($anggota->jurusan) {
                    $jurName = $anggota->jurusan->jurusan;
                    $counters[$jurName][$monthLabel] = ($counters[$jurName][$monthLabel] ?? 0) + 1;
                }
            }
        }

        $palette = [
            'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
            'rgba(201, 203, 207, 1)',
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($counters as $jurusan => $monthCounts) {
            $data = [];
            foreach ($months as $m) {
                $data[] = $monthCounts[$m] ?? 0;
            }

            $color = $palette[$colorIndex % count($palette)];
            $colorIndex++;

            $datasets[] = [
                'label'           => $jurusan,
                'data'            => $data,
                'borderColor'     => $color,
                'backgroundColor' => $color,
                'fill'            => false,
                'tension'         => 0.4,
            ];
        }

        return [
            'labels'   => $months,
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

    /**
     * Tipe chart
     */
    protected function getType(): string
    {
        return 'line';
    }
}

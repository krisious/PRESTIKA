<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Prestasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class BarPrestasiNonAkademik extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Grafik Bar Prestasi Nonakademik';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $startDate = $this->filters['start_date'] ?? null;
        $endDate = $this->filters['end_date'] ?? null;
        $jurusanId = $this->filters['jurusan_id'] ?? null;
        $tahunAjaranId = $this->filters['tahun_ajaran_id'] ?? null;

        // Ambil ID untuk kategori Nonakademik
        $kategoriNonakademikId = \App\Models\KategoriPrestasi::where('kategori', 'Nonakademik')->value('id');

        $query = Prestasi::select('subkategori_prestasis.subkategori', DB::raw('COUNT(*) as total'))
            ->join('subkategori_prestasis', 'prestasis.id_subkategori_prestasi', '=', 'subkategori_prestasis.id')
            ->where('prestasis.id_kategori_prestasi', $kategoriNonakademikId)
            ->where('prestasis.status', 'diterima')
            ->groupBy('subkategori_prestasis.subkategori');

        // Terapkan filter tanggal jika tersedia
        if (!empty($startDate) && !empty($endDate)) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('prestasis.tanggal_perolehan', [$start, $end]);
        }

        if (!empty($jurusanId)) {
            $query->whereHas('siswa', fn ($q) =>
                $q->where('id_jurusan', $jurusanId)
            );
        }

        if (!empty($tahunAjaranId)) {
            $query->where('prestasis.id_tahun_ajaran', $tahunAjaranId);
        }

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Prestasi',
                    'data' => $data->pluck('total'),
                    'backgroundColor' => 'rgba(5, 223, 113, 0.7)', 
                    'borderColor' => 'rgba(5, 223, 113, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->pluck('subkategori'),
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
        return 'bar';
    }
}

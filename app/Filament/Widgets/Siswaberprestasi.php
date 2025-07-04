<?php

namespace App\Filament\Widgets;

use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Siswaberprestasi extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top 5 Siswa Berprestasi';
    protected static ?int    $sort    = 1;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Sembunyikan jika user login & punya role 'Siswa'
        return ! (Auth::check() && Auth::user()->hasRole('Siswa'));
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        /* 🔍 Ambil filter global */
        $start          = $this->filters['start_date']      ?? null;
        $end            = $this->filters['end_date']        ?? null;
        $jurusanId      = $this->filters['jurusan_id']      ?? null;
        $tahunAjaranId  = $this->filters['tahun_ajaran_id'] ?? null;

        $startDate = $start ? Carbon::parse($start)->startOfDay() : null;
        $endDate   = $end   ? Carbon::parse($end)->endOfDay()     : null;

        /* 1️⃣  Prestasi sebagai siswa utama */
        $mainQuery = Prestasi::query()
            ->select('id_siswa as siswa_id', 'id')
            ->where('status', 'diterima')
            ->when($startDate && $endDate, fn ($q) =>
                $q->whereBetween('tanggal_perolehan', [$startDate, $endDate])
            )
            ->when($tahunAjaranId, fn ($q) =>
                $q->where('id_tahun_ajaran', $tahunAjaranId)
            );

        /* Prestasi sebagai anggota tim */
        $anggotaQuery = DB::table('prestasi_siswa')
            ->join('prestasis', 'prestasis.id', '=', 'prestasi_siswa.prestasi_id')
            ->select('prestasi_siswa.siswa_id', 'prestasis.id')
            ->where('prestasis.status', 'diterima')
            ->when($startDate && $endDate, fn ($q) =>
                $q->whereBetween('prestasis.tanggal_perolehan', [$startDate, $endDate])
            )
            ->when($tahunAjaranId, fn ($q) =>
                $q->where('prestasis.id_tahun_ajaran', $tahunAjaranId)
            );

        /* Gabungkan keduanya */
        $combined = $mainQuery->unionAll($anggotaQuery);

        /* Hitung total prestasi per siswa */
        $query = Siswa::query()
            ->joinSub($combined, 'all_prestasi', 'all_prestasi.siswa_id', '=', 'siswas.id')
            ->select('siswas.*', DB::raw('COUNT(DISTINCT all_prestasi.id) as total_prestasi'))
            ->groupBy('siswas.id')
            ->orderByDesc('total_prestasi')
            ->limit(5);

        /* 🏫 Filter jurusan (jika dipilih) */
        if ($jurusanId) {
            $query->where('siswas.id_jurusan', $jurusanId);
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Nama Siswa'),

            Tables\Columns\TextColumn::make('jurusan.jurusan')
                ->label('Jurusan'),

            Tables\Columns\TextColumn::make('total_prestasi')
                ->label('Total Prestasi'),
        ];
    }

    protected function getTableActions(): array
    {
        return []; // hanya tampilan
    }
}

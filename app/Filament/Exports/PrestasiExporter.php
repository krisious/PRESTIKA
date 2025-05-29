<?php

namespace App\Filament\Exports;

use App\Models\Prestasi;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrestasiExporter extends Exporter
{
    protected static ?string $model = Prestasi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('siswa.nis')->label('NIS'),
            ExportColumn::make('siswa.user.name')->label('Nama Siswa'),
            ExportColumn::make('siswa.jurusan.jurusan')->label('Jurusan'),
            ExportColumn::make('nama_lomba')->label('Nama Lomba'),
            ExportColumn::make('kategoriPrestasi.kategori')->label('Kategori Prestasi'),
            ExportColumn::make('subkategoriPrestasi.subkategori')->label('Subkategori Prestasi'),
            ExportColumn::make('tingkatPrestasi.tingkat')->label('Tingkat Prestasi'),
            ExportColumn::make('peringkatPrestasi.peringkat')->label('Peringkat Prestasi'),
            ExportColumn::make('delegasi.delegasi')->label('Delegasi'),
            ExportColumn::make('tanggal_perolehan')->label('Tanggal Perolehan')->formatStateUsing(
                fn ($state) => \Carbon\Carbon::parse($state)->format('d-m-Y')
            ),
            ExportColumn::make('penyelenggara')->label('Penyelenggara'),
            ExportColumn::make('lokasi')->label('Lokasi')
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prestasi export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}

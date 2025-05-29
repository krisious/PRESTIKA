<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Siswa;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Prestasi;
use App\Models\KategoriPrestasi;
use App\Models\SubkategoriPrestasi;
use Filament\Tables\Columns\TextColumn;

class LatestPrestasi extends BaseWidget
{
    protected static ?string $heading = 'Prestasi Terbaru';

    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full'; 

    protected static ?string $maxHeight = '100px';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Prestasi::query()
                    ->with(['kategoriPrestasi', 'subkategoriPrestasi'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('siswa.user.name')
                    ->label('Nama Siswa')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_lomba')
                    ->label('Nama Lomba')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_perolehan')
                    ->label('Tanggal Perolehan')
                    ->dateTime('d-m-Y')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
            ])->defaultSort(
                'created_at',
                'desc'
            );
    }
}

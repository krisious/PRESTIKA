<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanResource\Pages;
use Illuminate\Support\Facades\Storage;
use App\Models\Prestasi;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

class LaporanResource extends Resource
{
    protected static ?string $model = Prestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Prestasi';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $slug = 'laporan';
    protected static ?string $label = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kosong, karena hanya menampilkan data
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.user.name')
                    ->label('Nama Siswa')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('siswa.jurusan.jurusan')
                    ->label('Jurusan')
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

                TextColumn::make('kategoriPrestasi.kategori')
                    ->label('Kategori Prestasi')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subkategoriPrestasi.subkategori')
                    ->label('Subkategori Prestasi')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tingkatPrestasi.tingkat')
                    ->label('Tingkat Prestasi')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('peringkatPrestasi.peringkat')
                    ->label('Peringkat Prestasi')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('delegasi.delegasi')
                    ->label('Delegasi')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_perolehan')
                    ->label('Tanggal Perolehan')
                    ->dateTime()
                    ->copyable()
                    ->copyMessage('Copy to Clipboard'),

                TextColumn::make('lokasi')
                    ->label('Lokasi'),

                ImageColumn::make('bukti_prestasi')
                    ->disk('public')
                    ->label('Bukti Prestasi')
                    ->size(50)
                    ->url(fn (Prestasi $record): ?string =>
                        $record->bukti_prestasi ? Storage::url($record->bukti_prestasi) : null
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Tambahkan filter jika perlu
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Html2MediaAction::make('Print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->html2media()
                    ->savePdfAction()
                    ->printAction()
                    ->html2mediaOptions([
                        'elementId' => 'laporan',
                        'fileName' => 'laporan.pdf',
                        'savePdf' => true,
                        'print' => true,
                    ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Tambahkan Html2MediaAction untuk bulk jika ingin
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'edit' => Pages\EditLaporan::route('/{record}/edit'),
        ];
    }
}

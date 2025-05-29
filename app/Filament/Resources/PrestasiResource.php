<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestasiResource\Pages;
use App\Filament\Resources\PrestasiResource\RelationManagers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Prestasi;
use App\Models\KategoriPrestasi;
use App\Models\SubkategoriPrestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use App\Filament\Exports\PrestasiExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class PrestasiResource extends Resource
{
    protected static ?string $model = Prestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Prestasi';

    protected static ?string $navigationLabel = 'Prestasi';

    protected static ?string $slug = 'prestasi';
    
    protected static ?string $label = 'Prestasi';

    public static function getNavigationBadge(): ?string
    {
        if (Auth::check() && Auth::user()->hasAnyRole(['Admin', 'super_admin'])) {
            return static::getModel()::where('status', 'pending')->count();
        }

        return null; // Tidak menampilkan badge untuk role lain
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('id_siswa')
                    ->default(function () {
                        $user = Auth::user();
                        return Siswa::where('id_user', $user->id)->value('id');
                    })
                    ->default(fn () => optional(Siswa::where('id_user', Auth::id())->first())->id),
                TextInput::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->default(function () {
                        $user = Auth::user();
                        return $user->name;
                    })
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(2),
                TextInput::make('nama_lomba')
                    ->required()
                    ->label('Nama Lomba')
                    ->columnSpan(2)
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                Select::make('id_kategori_prestasi')
                    ->relationship('kategoriPrestasi', 'kategori')
                    ->required()
                    ->label('Kategori Prestasi')
                    ->reactive()
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                Select::make('id_subkategori_prestasi')
                    ->relationship('subkategoriPrestasi', 'subkategori')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Subkategori Prestasi')
                    ->options(function (callable $get) {
                        $kategoriId = $get('id_kategori_prestasi');
                        if (!$kategoriId) {
                            return [];
                        }
    
                        return SubkategoriPrestasi::where('id_kategori_prestasi', $kategoriId)
                            ->pluck('subkategori', 'id');
                    })
                    ->disabled(fn (callable $get) => !$get('id_kategori_prestasi'))
                    ->reactive()
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                Select::make('id_tingkat_prestasi')
                    ->relationship('tingkatPrestasi', 'tingkat')
                    ->required()
                    ->label('Tingkat Prestasi')
                    ->preload()
                    ->searchable()
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                Select::make('id_peringkat_prestasi')
                    ->relationship('peringkatPrestasi', 'peringkat')
                    ->required()
                    ->label('Peringkat Prestasi')
                    ->preload()
                    ->searchable()
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                Select::make('id_delegasi')
                    ->relationship('delegasi', 'delegasi')
                    ->required()
                    ->label('Delegasi')
                    ->preload()
                    ->searchable()
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                DatePicker::make('tanggal_perolehan')
                    ->required()
                    ->label('Tanggal Perolehan')
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                TextInput::make('lokasi')
                    ->required()
                    ->label('Lokasi')
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                TextInput::make('penyelenggara')
                    ->required()
                    ->label('Penyelenggara')
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                fileUpload::make('bukti_prestasi')
                    ->label('Bukti Prestasi')
                    ->disk('public')
                    ->directory('prestasi')
                    ->visibility('public')
                    ->downloadable()
                    ->required()
                    ->disabled(fn () => Auth::user()->hasRole('Admin') || Auth::user()->hasRole('super_admin')),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->label('Status')
                    ->disabled(fn () => Auth::user()->hasRole('Siswa')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
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
                    ->dateTime('d-m-Y')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->sortable(),
                TextColumn::make('penyelenggara')
                    ->label('Penyelanggara')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('bukti_prestasi')
                    ->label('Bukti Prestasi')
                    ->width(100),
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
            )
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('id_kategori_prestasi')
                    ->label('Kategori Prestasi')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('kategoriPrestasi', 'kategori'),
                Tables\Filters\SelectFilter::make('id_subkategori_prestasi')
                    ->label('Subkategori Prestasi')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('subkategoriPrestasi', 'subkategori'),
                Tables\Filters\SelectFilter::make('id_tingkat_prestasi')
                    ->label('Tingkat Prestasi')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('tingkatPrestasi', 'tingkat'),
                Tables\Filters\SelectFilter::make('id_peringkat_prestasi')
                    ->label('Peringkat Prestasi')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('peringkatPrestasi', 'peringkat'),
                Tables\Filters\SelectFilter::make('id_delegasi')
                    ->label('Delegasi')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->relationship('delegasi', 'delegasi'),
                Tables\Filters\Filter::make('tanggal_perolehan')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('tanggal_perolehan', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('tanggal_perolehan', '<=', $date));
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ]),
            ])->persistFiltersInSession()
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(function ($record): bool {
                    if (Auth::check() && Auth::user()->hasRole('Siswa')) {
                        return $record->status === 'ditolak';
                    }
                    return true; // Admin & superadmin bebas
                }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PrestasiExporter::class),
                Action::make('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(route('prestasi.print'))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),  
                ]),
                ExportBulkAction::make()
                    ->exporter(PrestasiExporter::class),
                BulkAction::make('cetak_pdf')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->action(function (Collection $records) {
                        $prestasi = $records->load([
                            'siswa.user',
                            'siswa.jurusan',
                            'kategoriPrestasi',
                            'subkategoriPrestasi',
                            'tingkatPrestasi',
                            'peringkatPrestasi',
                            'delegasi',
                        ])->sortBy('tanggal_perolehan');

                        $pdf = Pdf::loadView('pdf.prestasi', ['prestasi' => $prestasi])
                            ->setPaper('a4', 'landscape');

                    return response()->stream(function () use ($pdf) {
                        echo $pdf->output();
                    }, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="laporan-prestasi.pdf"',
                    ]);
                })
                ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrestasis::route('/'),
            'create' => Pages\CreatePrestasi::route('/create'),
            'edit' => Pages\EditPrestasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    
        if (Auth::check() && Auth::user()->hasRole('Siswa')) {
            $idSiswa = \App\Models\Siswa::where('id_user', Auth::user()->id)->value('id');
            return $query->where('id_siswa', $idSiswa);
        }
    
        return $query;
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestasiResource\Pages;
use App\Filament\Resources\PrestasiResource\RelationManagers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\User;
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
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrestasiResource extends Resource
{
    protected static ?string $model = Prestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Prestasi';

    protected static ?string $navigationLabel = 'Prestasi';

    protected static ?string $slug = 'prestasi';
    
    protected static ?string $label = 'Prestasi';

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
                    ->columnSpan(2),
                Select::make('id_kategori_prestasi')
                    ->relationship('kategoriPrestasi', 'kategori')
                    ->required()
                    ->label('Kategori Prestasi')
                    ->reactive(),
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
                    ->reactive(),
                Select::make('id_tingkat_prestasi')
                    ->relationship('tingkatPrestasi', 'tingkat')
                    ->required()
                    ->label('Tingkat Prestasi')
                    ->preload()
                    ->searchable(),
                Select::make('id_peringkat_prestasi')
                    ->relationship('peringkatPrestasi', 'peringkat')
                    ->required()
                    ->label('Peringkat Prestasi')
                    ->preload()
                    ->searchable(),
                Select::make('id_delegasi')
                    ->relationship('delegasi', 'delegasi')
                    ->required()
                    ->label('Delegasi')
                    ->preload()
                    ->searchable(),
                DatePicker::make('tanggal_perolehan')
                    ->required()
                    ->label('Tanggal Perolehan'),
                TextInput::make('lokasi')
                    ->required()
                    ->label('Lokasi'),
                TextInput::make('penyelenggara')
                    ->required()
                    ->label('Penyelenggara'),
                fileUpload::make('bukti_prestasi')
                    ->label('Bukti Prestasi')
                    ->disk('public')
                    ->directory('prestasi')
                    ->visibility('public')
                    ->downloadable()
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->label('Status')
                    ->disabled(),
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
                    ->url(fn (Prestasi $record): ?string => $record->bukti_prestasi ? Storage::url($record->bukti_prestasi) : null)
                    
            ])->defaultSort(
                'created_at',
                'desc'
            )
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),  
                ]),
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
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuruResource\Pages;
use App\Filament\Resources\GuruResource\RelationManagers;
use App\Models\Guru;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Pengguna';

    protected static ?string $navigationLabel = 'Guru';

    protected static ?string $slug = 'guru';    

    protected static ?string $label = 'Guru';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('id_user'),
                TextInput::make('user_name')
                    ->required()
                    ->label('Nama Lengkap')
                    ->dehydrated(false) // Jangan simpan ke model siswa
                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                        if ($record?->user) {
                            $component->state($record->user->name);
                        }
                    })
                    ->columnSpan(2),
                TextInput::make('user_email')
                    ->label('Email')
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                        if ($record?->user) {
                            $component->state($record->user->email);
                        }
                    })
                    ->email()
                    ->required()
                    ->columnSpan(2),
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->visibleOn(['create', 'edit'])
                    ->columnSpan(2)
                    ->helperText(fn (string $context) => $context === 'edit' 
                        ? 'Kosongkan jika tidak ingin mengganti password' 
                        : null),
                Select::make('status_pegawai')
                    ->required()
                    ->label('Status Pegawai')
                    ->options([
                        'asn' => 'ASN',
                        'non asn' => 'Non ASN',
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, $state) => $set('nip', null)),
                TextInput::make('nip')
                    ->label('NIP')
                    ->unique(ignoreRecord: true)
                    ->required(fn (callable $get) => $get('status_pegawai') === 'asn')
                    ->disabled(fn (callable $get) => $get('status_pegawai') === 'non asn')
                    ->reactive(),
                Select::make('jenis_kelamin')
                    ->required()
                    ->label('Jenis Kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ]),
                TextInput::make('tahun_masuk')
                    ->required()
                    ->label('Tahun Masuk')
                    ->numeric()
                    ->placeholder('YYYY'),
                Select::make('status')
                    ->required()
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non aktif' => 'Non Aktif',
                    ])
                    ->default('aktif'),
                Select::make('roles')
                    ->label('Roles')
                    ->options(Role::pluck('name', 'id'))
                    ->required()
                    ->dehydrated(false) 
                    ->default(fn () => [Role::where('name', 'Guru')->value('id')])
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record && $record->user) {
                            $roleIds = $record->user->roles->pluck('id')->toArray();
                            $component->state($roleIds ?: [Role::where('name', 'Guru')->value('id')]);
                    }
                    })
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Lengkap')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status_pegawai')
                    ->label('Status Pegawai')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'asn' => 'ASN',
                        'non asn' => 'Non ASN',
                    })
                    ->colors([
                        'success' => 'asn',
                        'warning' => 'non asn',
                    ])
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tahun_masuk')
                    ->label('Tahun Masuk')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'non aktif' => 'Non Aktif',
                    })
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'non aktif',
                    ])
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.roles.name')
                    ->copyable()
                    ->copyMessage('Copy to Clipboard')
                    ->searchable()
                    ->sortable(),
            ])
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
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
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

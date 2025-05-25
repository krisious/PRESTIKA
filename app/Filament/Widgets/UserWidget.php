<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;

class UserWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Admin', User::role('Admin')->count())
                ->description('Pengguna dengan peran Admin')
                ->icon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Total Guru Aktif', User::whereHas('guru', function ($query) {
                    $query->where('status', 'aktif');
                })->role('Guru')->count())
                ->description('Pengguna dengan peran Guru')
                ->icon('heroicon-o-user-group')
                ->color('warning'),

            Stat::make('Total Siswa Aktif', User::whereHas('siswa', function ($query) {
                    $query->where('status', 'aktif');
                })->role('Siswa')->count())
                ->description('Pengguna dengan peran Siswa')
                ->icon('heroicon-o-user-group')
                ->color('danger'),
        ];
    }
}

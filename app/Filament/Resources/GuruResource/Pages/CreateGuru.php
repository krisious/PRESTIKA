<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use App\Models\Guru;
use App\Models\User;
use Filament\Actions;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateGuru extends CreateRecord
{
    protected static string $resource = GuruResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $formState = $this->form->getRawState();

        if (!isset($formState['user_name'], $formState['user_email'], $formState['password'])) {
            throw new \Exception('Field name, email, atau password tidak tersedia.');
        }

        $this->validateSingleActivePrincipal(
            (array) ($formState['roles'] ?? []),
            $formState['status'] ?? 'aktif',
        );

        $user = User::create([
            'name' => $formState['user_name'],
            'email' => $formState['user_email'],
            'password' => bcrypt($formState['password']),
        ]);

        $formState['id_user'] = $user->id;

        // Hilangkan field yang bukan milik siswa
        unset($formState['user_name'], $formState['user_email'], $formState['password'], $formState['roles']);

        return $formState;
    }
    

    protected function afterCreate(): void
    {
        $user = $this->record->user;
        $formState = $this->form->getRawState(); 
    
        // $roles = $formState['roles'] ?? [];
        $roles = (array) ($formState['roles'] ?? []);
    
        if (!empty($roles) && $user) {
            // $user->syncRoles($roles);
            $roleNames = Role::whereIn('id', $roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
        } else {
            $defaultRole = Role::where('name', 'Guru')->first();
            if ($defaultRole && $user) {
                $user->assignRole($defaultRole);
            }
        }
    }

    protected function validateSingleActivePrincipal(string|array $roleIds, string $status): void
    {
        $roleIds = (array) $roleIds;
        $kepalaSekolahRoleId = Role::where('name', 'Kepala Sekolah')->value('id');

        // Jika bukan role Kepala Sekolah atau status tidak aktif, lewati validasi
        if (!in_array($kepalaSekolahRoleId, $roleIds) || $status !== 'aktif') {
            return;
        }

        // Cek apakah sudah ada Kepala Sekolah aktif lainnya
        $alreadyExists = Guru::where('status', 'aktif')
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Kepala Sekolah');
            })
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'roles' => 'Sudah ada akun Kepala Sekolah yang berstatus aktif. 
                            Nonaktifkan terlebih dahulu sebelum menetapkan akun baru.',
            ]);
        }
    }
}

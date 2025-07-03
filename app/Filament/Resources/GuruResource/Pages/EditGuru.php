<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Models\Guru;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $formState = $this->form->getRawState(); // Ambil semua state form

        if (!isset($formState['user_name'], $formState['user_email'])) {
            throw new \Exception('Field name atau email tidak tersedia.');
        }

        $this->validateSingleActivePrincipal(
            (array) ($formState['roles'] ?? []),
            $formState['status'] ?? 'aktif',
            $this->record->id,
        );

        // Update data User terlebih dahulu
        $user = $this->record->user;
        
        if ($user) {
            $user->update([
                'name' => $formState['user_name'],
                'email' => $formState['user_email'],
            ]);

            if (!empty($formState['password'])) {
                $user->update([
                    'password' => bcrypt($formState['password']),
                ]);
            }
        }

        $data['id_user'] = $user->id;

        // Bersihkan field non-model
        unset($data['user_name'], $data['user_email'], $data['roles'], $data['password']);

        return $data;
    }


    protected function afterSave(): void
    {
        $user = $this->record->user;
        $formState = $this->form->getRawState();

        if ($user) {
            // $roles = $formState['roles'] ?? [];
            $roles = (array) ($formState['roles'] ?? []);

            if (!empty($roles)) {
                // $user->syncRoles($roles);
                $roleNames = Role::whereIn('id', $roles)->pluck('name')->toArray();
                $user->syncRoles($roleNames);
            } else {
                // Fallback jika role kosong
                $defaultRole = Role::where('name', 'Guru')->first();
                if ($defaultRole) {
                    $user->syncRoles([$defaultRole->id]);
                }
            }
        }
    }

    protected function validateSingleActivePrincipal(string|array $roleIds, string $status, ?int $ignoreGuruId = null): void
    {
        $roleIds = (array) $roleIds;
        $kepalaSekolahRoleId = Role::where('name', 'Kepala Sekolah')->value('id');

        if (!in_array($kepalaSekolahRoleId, $roleIds) || $status !== 'aktif') {
            return; // tidak perlu validasi jika bukan kepala sekolah aktif
        }

        $alreadyExists = Guru::where('status', 'aktif')
            ->whereHas('user.roles', fn ($q) => $q->where('name', 'Kepala Sekolah'))
            ->when($ignoreGuruId, fn ($q) => $q->where('id', '!=', $ignoreGuruId))
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'roles' => 'Sudah ada akun Kepala Sekolah yang berstatus aktif. 
                            Nonaktifkan akun tersebut terlebih dahulu sebelum menetapkan akun ini.',
            ]);
        }
    }
}

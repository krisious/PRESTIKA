<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

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

        // Masukkan id_user ke dalam data Siswa
        $data['id_user'] = $user->id;

        // Hilangkan field yang tidak ada di tabel `siswas`
        unset($data['user_name'], $data['user_email'], $data['roles'], $data['password']);

        return $data;
    }


    protected function afterSave(): void
    {
        $user = $this->record->user;
        $formState = $this->form->getRawState();

        if ($user) {
            $roles = $formState['roles'] ?? [];

            if (!empty($roles)) {
                $user->syncRoles($roles);
            } else {
                // Jika roles tidak dipilih, pastikan tetap memiliki role "Siswa"
                $defaultRole = Role::where('name', 'Siswa')->first();
                if ($defaultRole) {
                $user->syncRoles([$defaultRole->id]);
                }
            }
        }
    }
}

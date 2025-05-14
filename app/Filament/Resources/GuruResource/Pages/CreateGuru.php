<?php

namespace App\Filament\Resources\GuruResource\Pages;

use App\Filament\Resources\GuruResource;
use App\Models\Guru;
use App\Models\User;
use Filament\Actions;
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
    
        $roles = $formState['roles'] ?? [];
    
        if (!empty($roles) && $user) {
            $user->syncRoles($roles);
        } else {
            $defaultRole = Role::where('name', 'Guru')->first();
            if ($defaultRole && $user) {
                $user->assignRole($defaultRole);
            }
        }
    }
}

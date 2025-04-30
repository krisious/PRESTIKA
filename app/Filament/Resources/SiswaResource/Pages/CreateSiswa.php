<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['name'], $data['email'], $data['password'])) {
            throw new \Exception('Field name, email, atau password tidak tersedia.');
        }
    
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    
        $data['id_user'] = $user->id;
    
        unset($data['name'], $data['email'], $data['password'], $data['roles']);
    
        return $data;
    }
    

    protected function afterCreate(): void
    {
        $user = $this->record->user;

        $formState = $this->form->getRawState(); 
    
        $roles = $formState['roles'] ?? [];
    
        if (!empty($roles) && $user) {
            $user->syncRoles($roles);
        } else {
            $defaultRole = Role::where('name', 'Siswa')->first();
            if ($defaultRole && $user) {
                $user->assignRole($defaultRole);
            }
        }
    }
}

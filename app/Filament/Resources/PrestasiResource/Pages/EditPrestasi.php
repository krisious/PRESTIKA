<?php

namespace App\Filament\Resources\PrestasiResource\Pages;

use App\Filament\Resources\PrestasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPrestasi extends EditRecord
{
    protected static string $resource = PrestasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    // Override this method to modify form data before filling the form
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (Auth::user()->hasRole('Siswa') && $data['status'] === 'ditolak') {
            // Set status to 'pending' before form is populated
            $data['status'] = 'pending';
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Auth::user()->hasRole('Siswa')) {
            $data['status'] = 'pending';
        }

        return $data;
    }

    public function mount($record): void
    {
        parent::mount($record);

        // Cek jika user adalah siswa
        if (Auth::user()->hasRole('Siswa')) {
            // Hanya bisa edit jika status 'ditolak'
            if ($this->record->status !== 'ditolak') {
                abort(403, 'Kamu hanya bisa mengedit prestasi yang ditolak.');
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


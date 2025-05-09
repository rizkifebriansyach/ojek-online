<?php

namespace App\Filament\Resources\DriverTrackingResource\Pages;

use App\Filament\Resources\DriverTrackingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriverTracking extends EditRecord
{
    protected static string $resource = DriverTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

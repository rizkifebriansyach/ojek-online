<?php

namespace App\Filament\Resources\DriverTrackingResource\Pages;

use App\Filament\Resources\DriverTrackingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverTrackings extends ListRecords
{
    protected static string $resource = DriverTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'admin' => Tab::make('Admins')
                ->icon('heroicon-o-shield-check')
                ->badge(fn() => User::where('role', 'admin')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'admin')),
            'customer' => Tab::make('Customers')
                ->icon('heroicon-o-user-group')
                ->badge(fn() => User::where('role', 'customer')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'customer')),
            'driver' => Tab::make('Drivers')
                ->icon('heroicon-o-truck')
                ->badge(fn() => User::where('role', 'driver')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'driver')),
        ];
    }
}

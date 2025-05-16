<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name', function ($query) {
                    return $query->where('role', 'customer');
                })
                    ->required(),
                Forms\Components\Select::make('driver_id')
                ->options(function () {
                    return Driver::with('user')
                        ->whereHas('user', function ($query) {
                            $query->where('role', 'driver');
                        })
                        ->get()
                        ->mapWithKeys(function ($driver) {
                            return [$driver->id => $driver->user->name . '-' . $driver->vehicle_number];
                        })
                        ->toArray();
                })
                ->nullable()
                ->label('Driver'),
                Forms\Components\TextInput::make('latitude_origin')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('longitude_origin')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('address_origin')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude_destination')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('longitude_destination')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('address_destination')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('distance')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                ->prefix('Rp'),
            Forms\Components\Select::make('status')
                ->options([
                    'finding_driver' => 'Finding Driver',
                    'driver_pickup' => 'Driver Pickup',
                    'driver_deliver' => 'Driver Deliver',
                    'arrived' => 'Arrived',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled'
                ])
                    ->required(),
                Forms\Components\TextInput::make('time_estimate')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver.user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude_origin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude_origin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address_origin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude_destination')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude_destination')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address_destination')
                    ->searchable(),
                Tables\Columns\TextColumn::make('distance')
                    ->label('Distance (km)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'finding_driver' => 'gray',
                        'driver_pickup' => 'warning',
                        'driver_deliver' => 'info',
                        'arrived' => 'success',
                        'paid' => 'success',
                        'cancelled' => 'danger'
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('time_estimate')
                    ->label('Time Estimate (second)')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}

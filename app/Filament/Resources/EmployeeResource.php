<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\city;
use App\Models\Employee;
use App\Models\state;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;


class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-User';
    protected static ?string $navigationGroup = 'Employee Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Names')
                    ->description('Fill in the Employee names')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),
               Forms\Components\Section::make('Addresses')
                   ->description('Fill in the Employee address')
                   ->schema([
                       Forms\Components\Select::make('country_id')
                           ->relationship('country', 'name')
                           ->preload()
                           ->live()
                           ->searchable()
                           ->afterStateUpdated(function(Set $set){
                               $set('city_id', null);
                               $set('state_id', null);
                           })
                           ->required(),
                       Forms\Components\Select::make('state_id')
                           ->options(fn(Get $get): Collection => State::query()
                           ->where('country_id', $get('country_id'))
                               ->pluck('name', 'id'))
                           ->preload()
                           ->live()
                           ->searchable()
                           ->afterStateUpdated(fn(Set $set) => $set('city_id', null))
                           ->required(),
                       Forms\Components\Select::make('city_id')
                           ->options(fn(Get $get): Collection => City::query()
                               ->where('state_id', $get('state_id'))
                               ->pluck('name', 'id'))
                           ->preload()
                           ->searchable()
                           ->live()
                           ->required(),
                       Forms\Components\Select::make('department_id')
                           ->relationship('department', 'name')
                           ->required(),
                       Forms\Components\TextInput::make('address')
                           ->required()
                           ->maxLength(255),
                   ])->columns(3),
                Forms\Components\Section::make('Info')->schema([
                    Forms\Components\DatePicker::make('birthday')
                        ->native(false)
                    ->required(),
                    Forms\Components\TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(16),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Filter')
                ->relationship('department', 'name')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}

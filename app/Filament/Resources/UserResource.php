<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Administrar Roles y Permisos';
    protected static ?string $navigationLabel = 'Cuentas de Usuarios';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\Select::make('roles')
                    ->label('Rol de cuenta/Permisos')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('rol_tipo')
                    ->label('Rol')
                    ->options([
                        'Cliente' => 'Cliente',
                        'Empleado' => 'Empleado',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\Select::make('rol')
                    ->label('Tipo de Empleado')
                    ->options([
                        'Adm. Finanzas' => 'Adm. Finanzas',
                        'Comercial' => 'Comercial',
                        'Técnico' => 'Técnico',
                        'Soporte' => 'Soporte',
                        'PMO' => 'PMO',
                        'G. Humanas' => 'G. Humanas',
                        'QA' => 'QA',
                        'UX' => 'UX',
                        'Agencia' => 'Agencia',
                        'Calidad' => 'Calidad',
                    ])
                    ->required()
                    ->visible(fn ($get) => $get('rol_tipo') === 'Empleado'),
                Forms\Components\Hidden::make('rol')
                    ->default('Cliente')
                    ->visible(fn ($get) => $get('rol_tipo') === 'Cliente'),
                Forms\Components\TextInput::make('carga_horaria_mensual')
                    ->numeric()
                    ->label('Carga horaria mensual (h)')
                    ->nullable(),

                Forms\Components\TextInput::make('clockify_id')
                    ->label('ID Clockify')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable()
                    ->label('Rol de cuenta/Permisos')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

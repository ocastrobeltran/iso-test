<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Filament\Resources\UsuarioResource\RelationManagers;
use App\Models\Usuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Clientes y empleados';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255),
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
                        'PM' => 'PM',
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rol')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
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
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit' => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}

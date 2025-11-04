<?php

namespace App\Filament\Clientes\Resources\TicketResource\Pages;

use App\Filament\Clientes\Resources\TicketResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function resolveRecord($key): Model
    {
        return static::getModel()::with(['contrato.fees', 'contrato.proyectos'])->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make()
            //     ->visible(fn () => $this->record->estado !== 'Cerrado'),

            Actions\Action::make('agregar_comentario')
                ->label('Agregar Comentario')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->visible(fn () => $this->record->estado !== 'Cerrado')
                ->form([
                    Forms\Components\Textarea::make('comentario')
                        ->label('Comentario')
                        ->required()
                        ->rows(4)
                        ->placeholder('Escribe tu comentario aquí...')
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        // Obtener registro fresco con lock
                        $ticket = static::getModel()::lockForUpdate()->find($this->record->id);
                        
                        // IMPORTANTE: acceder al atributo raw, no al accessor
                        $rawComentarios = $ticket->getAttributes()['comentarios'] ?? null;
                        $comentarios = is_string($rawComentarios) ? json_decode($rawComentarios, true) : $rawComentarios;
                        $comentarios = is_array($comentarios) ? $comentarios : [];
                        
                        $comentarios[] = [
                            'usuario_id' => auth()->id(),
                            'contenido' => $data['comentario'],
                            'fecha' => now()->toDateTimeString(),
                        ];

                        // Forzar guardado directo sin pasar por accessor
                        $ticket->setRawAttributes(array_merge($ticket->getAttributes(), [
                            'comentarios' => json_encode($comentarios)
                        ]));
                        $ticket->save();
                    });

                    Notification::make()->title('Comentario agregado')->success()->send();

                    redirect()->to(static::getUrl(['record' => $this->record]));
                })
                ->modalHeading('Agregar Comentario')
                ->modalSubmitActionLabel('Guardar'),

            Actions\Action::make('cerrar_ticket')
                ->label('Marcar como Cerrado')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->estado === 'Resuelto')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('comentario_cierre')
                        ->label('Comentario de cierre (opcional)')
                        ->rows(3)
                        ->placeholder('¿Deseas agregar un comentario sobre el cierre?')
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        $ticket = static::getModel()::lockForUpdate()->find($this->record->id);
                        
                        $ticket->estado = 'Cerrado';
                        $ticket->fecha_cierre = now();

                        if (!empty($data['comentario_cierre'])) {
                            $comentarios = is_array($ticket->comentarios) ? $ticket->comentarios : [];
                            
                            $comentarios[] = [
                                'usuario_id' => auth()->id(),
                                'contenido' => '[CIERRE] ' . $data['comentario_cierre'],
                                'fecha' => now()->toDateTimeString(),
                            ];
                            
                            $ticket->comentarios = $comentarios;
                        }

                        $ticket->save();
                    });

                    Notification::make()->title('Ticket cerrado exitosamente')->success()->send();

                    redirect()->to(static::getUrl(['record' => $this->record]));
                })
                ->modalHeading('Confirmar Cierre de Ticket')
                ->modalDescription('¿Estás seguro de que la solución proporcionada resolvió tu problema?')
                ->modalSubmitActionLabel('Cerrar Ticket'),

            Actions\Action::make('devolver_ticket')
                ->label('Devolver Ticket')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->visible(fn () => $this->record->estado === 'Resuelto')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Textarea::make('razon_devolucion')
                        ->label('Razón de la devolución')
                        ->required()
                        ->rows(4)
                        ->placeholder('Explica por qué la solución no fue satisfactoria...')
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        $ticket = static::getModel()::lockForUpdate()->find($this->record->id);
                        
                        $ticket->estado = 'Devuelto';
                        $ticket->fecha_resolucion = null;

                        $comentarios = is_array($ticket->comentarios) ? $ticket->comentarios : [];
                        
                        $comentarios[] = [
                            'usuario_id' => auth()->id(),
                            'contenido' => '[DEVUELTO] ' . $data['razon_devolucion'],
                            'fecha' => now()->toDateTimeString(),
                        ];
                        
                        $ticket->comentarios = $comentarios;
                        $ticket->save();
                    });

                    Notification::make()
                        ->title('Ticket devuelto')
                        ->body('El equipo técnico revisará tu comentario.')
                        ->warning()
                        ->send();

                    redirect()->to(static::getUrl(['record' => $this->record]));
                })
                ->modalHeading('Devolver Ticket')
                ->modalDescription('Por favor, explica qué está pendiente o por qué la solución no fue satisfactoria.')
                ->modalSubmitActionLabel('Devolver Ticket'),
        ];
    }
}
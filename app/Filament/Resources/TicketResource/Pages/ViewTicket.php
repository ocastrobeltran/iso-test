<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->visible(fn () => strtolower(trim($this->record->estado)) !== 'cerrado')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->url(fn () => $this->getResource()::getUrl('edit', ['record' => $this->record])),

            Actions\Action::make('agregar_comentario')
                ->label('Agregar Comentario')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->visible(fn () => strtolower(trim($this->record->estado)) !== 'cerrado')
                ->form([
                    Forms\Components\Textarea::make('comentario')
                        ->label('Comentario')
                        ->required()
                        ->rows(4)
                        ->placeholder('Escribe tu comentario aquí...'),
                ])
                ->action(function (array $data): void {
                    $this->appendComentario($data['comentario']);
                    Notification::make()->title('Comentario agregado')->success()->send();

                    // Recargar la misma vista sin retornar valor
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]), navigate: true);
                }),

            Actions\Action::make('marcar_en_progreso')
                ->label('Marcar En Progreso')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->visible(fn () => in_array($this->record->estado, ['Abierto', 'Devuelto']))
                ->form([
                    Forms\Components\Textarea::make('comentario')
                        ->label('Comentario (opcional)')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        $ticket = static::getModel()::lockForUpdate()->find($this->record->id);

                        $updates = ['estado' => 'En Progreso'];
                        if (empty($ticket->fecha_asignacion)) {
                            $updates['fecha_asignacion'] = now();
                        }

                        $ticket->fill($updates)->save();

                        if (!empty($data['comentario'])) {
                            $this->appendComentario('[EN PROGRESO] ' . $data['comentario'], insideTransaction: true);
                        }
                    });

                    Notification::make()->title('Ticket marcado En Progreso')->success()->send();
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]), navigate: true);
                }),

            Actions\Action::make('marcar_resuelto')
                ->label('Marcar como Resuelto')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->estado === 'En Progreso')
                ->form([
                    Forms\Components\Textarea::make('solucion')
                        ->label('Solución aportada')
                        ->required()
                        ->rows(4)
                        ->placeholder('Describe la solución implementada...'),
                    Forms\Components\Textarea::make('comentario')
                        ->label('Comentario (opcional)')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    DB::transaction(function () use ($data) {
                        $ticket = static::getModel()::lockForUpdate()->find($this->record->id);

                        $ticket->fill([
                            'estado' => 'Resuelto',
                            'solucion' => $data['solucion'],
                            'fecha_resolucion' => now(),
                        ])->save();

                        if (!empty($data['comentario'])) {
                            $this->appendComentario('[RESUELTO] ' . $data['comentario'], insideTransaction: true);
                        }
                    });

                    Notification::make()->title('Ticket marcado como Resuelto')->success()->send();
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]), navigate: true);
                }),

            // Actions\Action::make('cerrar_ticket')
            //     ->label('Cerrar Ticket')
            //     ->icon('heroicon-o-lock-closed')
            //     ->color('gray')
            //     ->visible(fn () => $this->record->estado === 'Resuelto')
            //     ->form([
            //         Forms\Components\Textarea::make('comentario')
            //             ->label('Comentario de cierre (opcional)')
            //             ->rows(3),
            //     ])
            //     ->action(function (array $data): void {
            //         DB::transaction(function () use ($data) {
            //             $ticket = static::getModel()::lockForUpdate()->find($this->record->id);

            //             $ticket->fill([
            //                 'estado' => 'Cerrado',
            //                 'fecha_cierre' => now(),
            //             ])->save();

            //             if (!empty($data['comentario'])) {
            //                 $this->appendComentario('[CIERRE] ' . $data['comentario'], insideTransaction: true);
            //             }
            //         });

            //         Notification::make()->title('Ticket cerrado')->success()->send();
            //         $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]), navigate: true);
            //     }),

            // Actions\Action::make('devolver_ticket')
            //     ->label('Devolver Ticket')
            //     ->icon('heroicon-o-arrow-uturn-left')
            //     ->color('danger')
            //     ->visible(fn () => $this->record->estado === 'Resuelto')
            //     ->form([
            //         Forms\Components\Textarea::make('comentario')
            //             ->label('Razón de la devolución')
            //             ->required()
            //             ->rows(4)
            //             ->placeholder('Explica por qué considera que falta algo...'),
            //     ])
            //     ->action(function (array $data): void {
            //         DB::transaction(function () use ($data) {
            //             $ticket = static::getModel()::lockForUpdate()->find($this->record->id);

            //             $ticket->fill([
            //                 'estado' => 'Devuelto',
            //             ])->save();

            //             $this->appendComentario('[DEVUELTO] ' . $data['comentario'], insideTransaction: true);
            //         });

            //         Notification::make()
            //             ->title('Ticket devuelto')
            //             ->body('Se registró la razón de la devolución.')
            //             ->warning()
            //             ->send();

            //         $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]), navigate: true);
            //     }),
        ];
    }

    // Apendea un comentario sin sobrescribir, de forma segura.
    private function appendComentario(string $contenido, bool $insideTransaction = false): void
    {
        $append = function () use ($contenido) {
            $ticket = static::getModel()::lockForUpdate()->find($this->record->id);

            // Leer valor crudo para evitar mutadores
            $raw = $ticket->getRawOriginal('comentarios');
            $comentarios = [];

            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $comentarios = $decoded;
                }
            } elseif (is_array($ticket->getAttribute('comentarios'))) {
                $comentarios = $ticket->getAttribute('comentarios');
            }

            $comentarios[] = [
                'usuario_id' => auth()->id(),
                'contenido' => $contenido,
                'fecha' => now()->toDateTimeString(),
            ];

            // Persistir; con cast array Eloquent serializa a JSON.
            $ticket->setAttribute('comentarios', $comentarios);
            $ticket->save();
        };

        if ($insideTransaction) {
            $append();
            return;
        }

        DB::transaction($append);
    }

    protected function resolveRecord($key): Model
    {
        return static::getModel()::with(['contrato.fees', 'contrato.proyectos'])->findOrFail($key);
    }
}
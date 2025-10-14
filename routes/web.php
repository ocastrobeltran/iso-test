<?php

use Illuminate\Support\Facades\Route;

Route::post('/exportar-tickets-csv', [\App\Filament\Pages\ReporteDashboard::class, 'exportarTicketsCsv'])->name('exportar.tickets.csv');

// Route::get('/', function () {
//     return view('welcome');
// });

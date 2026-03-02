<?php

use App\Livewire\Projects\Index as ProjectsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::livewire('/projects', ProjectsIndex::class)
        ->name('projects.index');

    require __DIR__.'/settings.php';
});
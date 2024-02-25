<?php

use App\Actions\CreateDocuments;
use App\Actions\CreatePropertyDocuments;
use App\Models\Document;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('index {--delete} {filename}', function ($filename, CreateDocuments $createDocuments) {
    $documents = collect(json_decode(file_get_contents($filename), true))->take(300);
    $bar = $this->output->createProgressBar($documents->count());
    $createDocuments->execute($documents, $bar, $this->option('delete'));
    $this->info('');
    $this->info('Documents indexed successfully');
})->purpose('Index documents');

Artisan::command('properties:index {--delete} {--limit=3} {filename}', function ($filename, CreatePropertyDocuments $createDocuments) {
    $documents = collect(json_decode(file_get_contents($filename), true))->take(300);
    $bar = $this->output->createProgressBar($documents->count());
    $createDocuments->execute($documents, $bar, $this->option('delete'));
    $this->info('');
    $this->info('Documents indexed successfully');
})->purpose('Index documents');

<?php

use App\Actions\CreateDocuments;
use App\Models\Document;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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

Artisan::command('index {filename}', function ($filename, CreateDocuments $createDocuments) {
    $documents = collect(json_decode(file_get_contents($filename), true));
    $createDocuments->execute($documents->take(300));
    $this->info('Documents indexed successfully');
})->purpose('Index documents');

Artisan::command('search {query*}', function ($query) {
    $documents = Document::search(implode(' ', $query));

    foreach ($documents as $document) {
        $this->line("# {$document->title}");
        $this->line('');
        $this->line($document->content);
        $this->line('');
        $this->line((int) ($document->_score * 100) . '%');
        $this->line('---');
        $this->line('');
    }
})->purpose('Search documents');

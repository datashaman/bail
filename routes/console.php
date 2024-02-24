<?php

use App\Models\Document;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use OpenAI\Laravel\Facades\OpenAI;
use Pgvector\Laravel\Vector;

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

Artisan::command('search {query*}', function ($query) {
    $query = implode(' ', $query);

    $result = OpenAI::embeddings()->create([
        'model' => config('openai.embedding_model'),
        'input' => $query,
    ]);

    $embedding = new Vector($result->embeddings[0]->embedding);

    $documents = Document::query()
        ->select('title')
        ->selectRaw('1 - (embedding <=> ?) as similarity', [$embedding])
        ->orderBy('similarity', 'desc')
        ->take(2)
        ->get();

    $documents = $documents
        ->map(fn ($document) => [$document->title, $document->similarity])
        ->all();

    $this->table(['Document', 'Similarity'], $documents);
})->purpose('Search documents');

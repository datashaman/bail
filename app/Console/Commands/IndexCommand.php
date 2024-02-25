<?php

namespace App\Console\Commands;

use App\Actions\CreateDocuments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class IndexCommand extends Command
{
    protected $signature = 'index {filename : The filename of the documents to index}
        {--chunk=5 : Chunk size of the documents}
        {--limit= : Limit the number of documents to index}
        {--delete : Delete all documents before indexing}
    ';
    protected $description = 'Index documents.';

    public function handle(CreateDocuments $createDocuments)
    {
        $contents = File::get($this->argument('filename'));
        $documents = collect(json_decode($contents, true));

        if ($limit = $this->option('limit')) {
            $documents = $documents->take($limit);
        }

        $bar = $this->output->createProgressBar($documents->count());

        $createDocuments->execute($documents, $bar, $this->option('delete'));

        $this->info('');
    }
}

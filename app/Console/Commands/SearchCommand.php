<?php

namespace App\Console\Commands;

use App\Actions\CreateEmbeddings;
use App\Models\Document;
use Illuminate\Console\Command;
use Pgvector\Laravel\Vector;

class SearchCommand extends Command
{
    protected $signature = 'search {query* : The query to search for}
        {--per-page=10 : The number of results to return per page}
        {--page=1 : The page number to return}
        {--embedding : Use pgvector search, default is text search}
    ';
    protected $description = 'Search documents';

    public function handle(CreateEmbeddings $createEmbeddings)
    {
        $query = implode(' ', $this->argument('query'));

        if ($this->option('embedding')) {
            $result = $createEmbeddings->execute([['content' => $query]]);
            $query = new Vector($result->embeddings[0]->embedding);
        }

        $this->line(
            Document::search(
                $query,
                $this->option('per-page'),
                $this->option('page')
            )
            ->toJson(JSON_PRETTY_PRINT)
        );
    }
}

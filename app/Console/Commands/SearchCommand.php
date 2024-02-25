<?php

namespace App\Console\Commands;

use App\Actions\CreateEmbeddings;
use App\Models\Document;
use Illuminate\Console\Command;
use Pgvector\Laravel\Vector;

class SearchCommand extends Command
{
    protected $signature = 'search {--per-page=10} {--page=1} {--embedding} {query*}';
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

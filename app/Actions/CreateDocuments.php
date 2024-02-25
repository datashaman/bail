<?php

namespace App\Actions;

use App\Models\Document;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class CreateDocuments
{
    public function __construct(
        protected CreateEmbeddings $createEmbeddings
    ) {
    }

    public function execute(
        array|Collection $documents,
        ?ProgressBar $bar = null,
        bool $delete = false,
        int $chunk = 5
    ): Collection {
        $documents = collect($documents);

        if ($delete) {
            Document::query()->delete();
        }

        return $documents
            ->chunk($chunk)
            ->map(function ($chunk) use ($bar) {
                $chunk = $chunk
                    ->values()
                    ->transform($this->transformDocument(...));

                $embeddings = $this
                    ->createEmbeddings
                    ->execute($chunk)
                    ->embeddings;

                if ($chunk->count() !== count($embeddings)) {
                    throw new \Exception('Embeddings count does not match documents count');
                }

                $documents = [];

                foreach ($embeddings as $embedding) {
                    $document = $chunk[$embedding->index];

                    $documents[] = Document::create([
                        'hash' => Document::hash($document),
                        'content' => trim($document['content']),
                        'meta' => Arr::get($document, 'meta', []),
                        'embedding' => $embedding->embedding,
                    ]);

                    if ($bar) {
                        $bar->advance();
                    }
                }

                return $documents;
            })
            ->flatten();
    }

    protected function transformDocument(array $document): array
    {
        return $document;
    }
}

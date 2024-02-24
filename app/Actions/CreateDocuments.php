<?php

namespace App\Actions;

use App\Models\Document;
use Illuminate\Support\Collection;

class CreateDocuments
{
    public function __construct(
        protected CreateEmbeddings $createEmbeddings
    ) {
    }

    public function execute(array|Collection $documents): Collection
    {
        $documents = collect($documents);

        return $documents
            ->chunk(10)
            ->map(function ($chunk) {
                $chunk = $chunk->values();

                $embeddings = $this
                    ->createEmbeddings
                    ->execute($chunk)
                    ->embeddings;

                foreach ($embeddings as $embedding) {
                    $document = $chunk[$embedding->index];

                    return Document::updateOrCreate([
                        'hash' => md5(json_encode([
                            'content' => $document['content'],
                            'meta' => [],
                        ])),
                    ], [
                        'title' => trim($document['title']),
                        'content' => trim($document['content']),
                        'meta' => [],
                        'content_type' => 'text/plain',
                        'embedding' => $embedding->embedding,
                    ]);
                }
            });
    }
}

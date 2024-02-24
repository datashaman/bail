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

    public function execute(array|Collection $documents, ?ProgressBar $bar = null, bool $deleteExisting = false): Collection
    {
        $documents = collect($documents);

        if ($deleteExisting) {
            Document::query()->delete();
        }

        return $documents
            ->chunk(10)
            ->map(function ($chunk) use ($bar) {
                $chunk = $chunk
                    ->values()
                    ->transform($this->transformDocument(...));

                $embeddings = $this
                    ->createEmbeddings
                    ->execute($chunk)
                    ->embeddings;

                $documents = [];

                foreach ($embeddings as $embedding) {
                    $document = $chunk[$embedding->index];

                    $documents[] = Document::updateOrCreate([
                        'hash' => Document::hash($document),
                    ], [
                        'title' => trim($document['title']),
                        'content' => trim($document['content']),
                        'meta' => Arr::get($document, 'meta', []),
                        'content_type' => $document['content_type'],
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

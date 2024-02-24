<?php

namespace App\Actions;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;

class CreateEmbeddings
{
    public function execute(string|array|Collection $documents)
    {
        $hash = md5(
            collect($documents)
                ->map(
                    fn ($document) => Document::hash($document)
                )
                ->implode('-')
        );

        return Cache::rememberForever(
            "embeddings-{$hash}",
            fn () => $this->createEmbeddings($documents)
        );
    }

    protected function createEmbeddings(string|array|Collection $documents)
    {
        $params = [
            'model' => config('openai.embedding_model'),
            'input' => is_string($documents) ? $documents : collect($documents)->pluck('content')->all(),
        ];

        return OpenAI::embeddings()->create($params);
    }
}

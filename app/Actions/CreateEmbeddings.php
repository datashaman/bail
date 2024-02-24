<?php

namespace App\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;

class CreateEmbeddings
{
    public function execute(string|array|Collection $documents)
    {
        return Cache::remember(
            'embeddings_' . md5(serialize($documents)),
            now()->addDays(7),
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

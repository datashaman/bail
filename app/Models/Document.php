<?php

namespace App\Models;

use App\Actions\CreateEmbeddings;
use App\Observers\DocumentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pgvector\Laravel\Vector;

#[ObservedBy(DocumentObserver::class)]
class Document extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    protected $casts = [
        'embedding' => Vector::class,
        'meta' => 'array',
    ];

    public static function hash(array $data): string
    {
        $meta = json_encode(Arr::get($data, 'meta', []));
        $embedding = json_encode(Arr::get($data, 'embedding', []));

        return hash('sha256', "{$data['content']}-{$data['content_type']}-{$meta}-{$embedding}");
    }

    public function scopeSearch($query, string|array $search, int $limit = 3): Builder
    {
        if (is_array($query)) {
            $query = implode(' ', $query);
        }

        $result = resolve(CreateEmbeddings::class)->execute($query->get());
        $embedding = new Vector($result->embeddings[0]->embedding);

        return Document::query()
            ->select('title', 'content')
            ->selectRaw('1 - (embedding <=> ?) as _score', [$embedding])
            ->orderBy('_score', 'desc')
            ->take($limit);
    }
}

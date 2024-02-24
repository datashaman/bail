<?php

namespace App\Models;

use App\Actions\CreateEmbeddings;
use App\Observers\DocumentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public static function search(string $query, int $limit = 3): Collection
    {
        $result = resolve(CreateEmbeddings::class)->execute($query);
        $embedding = new Vector($result->embeddings[0]->embedding);

        return static::query()
            ->select('title', 'content')
            ->selectRaw('1 - (embedding <=> ?) as _score', [$embedding])
            ->orderBy('_score', 'desc')
            ->take($limit)
            ->get();
    }
}

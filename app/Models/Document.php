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
use Illuminate\Support\Facades\DB;
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

    public static function hash(array|Document $data): string
    {
        $meta = json_encode(data_get($data, 'meta', []));
        $embedding = json_encode(data_get($data, 'embedding', []));

        return hash('sha256', "{$data['content']}-{$meta}-{$embedding}");
    }

    public static function search(string|array $search, int $limit = 3): Collection
    {
        if (is_array($search)) {
            $search = implode(' ', $search);
        }

        $result = resolve(CreateEmbeddings::class)->execute([['content' => $search]]);
        $embedding = new Vector($result->embeddings[0]->embedding);

        return Document::query()
            ->select('content', 'meta')
            ->selectRaw('embedding <=> ? as _distance', [$embedding])
            ->orderBy('_distance')
            ->take($limit)
            ->get();
    }
}

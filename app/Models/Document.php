<?php

namespace App\Models;

use App\Actions\CreateEmbeddings;
use App\Observers\DocumentObserver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public static function search(string|array|Vector $query, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        if (is_array($query)) {
            $query = implode(' ', $query);
        }

        if (is_string($query)) {
            return Document::query()
                ->select('id', 'content', 'meta')
                ->selectRaw("ts_rank_cd(tsv, websearch_to_tsquery('english', ?)) as _score", [$query])
                ->whereRaw("tsv @@ websearch_to_tsquery('english', ?)", [$query])
                ->orderBy('_score', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return Document::query()
            ->select('id', 'content', 'meta')
            ->selectRaw('1 - (embedding <=> ?) as _score', [$query])
            ->whereRaw("1 - (embedding <=> ?) > ?", [$query, 0.33])
            ->orderBy('_score', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}

<?php

namespace App\Models;

use App\Observers\DocumentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}

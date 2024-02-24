<?php

namespace App\Observers;

use App\Models\Document;

class DocumentObserver
{
    public function saving(Document $document): void
    {
        if (!$document->hash) {
            $document->hash = md5(json_encode([
                'content' => $document->content,
                'meta' => $document->meta,
            ]));
        }
    }
}

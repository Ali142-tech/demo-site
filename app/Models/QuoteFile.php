<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteFile extends Model
{
    protected $fillable = ['quote_request_id', 'path', 'original_name', 'size'];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function isImage(): bool
    {
        return in_array(strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    public function isViewable(): bool
    {
        return $this->isImage() || strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function humanSize(): string
    {
        $bytes = $this->size;
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024 || $unit === 'GB') {
                return round($bytes, $unit === 'B' ? 0 : 1).' '.$unit;
            }
            $bytes /= 1024;
        }

        return '';
    }
}

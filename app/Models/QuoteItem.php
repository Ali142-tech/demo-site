<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = ['quote_request_id', 'product_id', 'name', 'quantity', 'specs', 'unit_price'];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2'];
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lineTotal(): ?float
    {
        return $this->unit_price === null ? null : (float) $this->unit_price * $this->quantity;
    }
}

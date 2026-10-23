<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    public const STATUSES = [
        'pending' => ['label' => 'Awaiting quotation', 'tone' => 'warn'],
        'quoted' => ['label' => 'Quotation ready', 'tone' => 'info'],
        'accepted' => ['label' => 'Accepted', 'tone' => 'ok'],
        'in_production' => ['label' => 'In production', 'tone' => 'violet'],
        'ready' => ['label' => 'Ready for delivery', 'tone' => 'ok'],
        'completed' => ['label' => 'Completed', 'tone' => 'done'],
        'declined' => ['label' => 'Declined', 'tone' => 'bad'],
        'cancelled' => ['label' => 'Cancelled', 'tone' => 'bad'],
    ];

    protected $fillable = [
        'reference', 'user_id', 'title', 'status', 'needed_by', 'notes', 'delivery_address',
        'delivery_fee', 'discount', 'quoted_total', 'quote_notes', 'quote_valid_until',
        'quoted_at', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'needed_by' => 'date',
            'quote_valid_until' => 'date',
            'quoted_at' => 'datetime',
            'responded_at' => 'datetime',
            'delivery_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'quoted_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $quote) {
            $quote->reference ??= self::newReference();
        });
    }

    public static function newReference(): string
    {
        do {
            $ref = 'Q-'.now()->format('ymd').'-'.strtoupper(\Illuminate\Support\Str::random(4));
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(QuoteFile::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(QuoteMessage::class)->with('user')->oldest();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function statusTone(): string
    {
        return self::STATUSES[$this->status]['tone'] ?? 'info';
    }

    public function itemsSubtotal(): float
    {
        return (float) $this->items->sum(fn ($i) => $i->lineTotal() ?? 0);
    }

    public function computeTotal(): float
    {
        return max(0, $this->itemsSubtotal() + (float) $this->delivery_fee - (float) $this->discount);
    }

    public function isQuoteExpired(): bool
    {
        return $this->status === 'quoted' && $this->quote_valid_until && $this->quote_valid_until->isPast();
    }

    public function canCustomerRespond(): bool
    {
        return $this->status === 'quoted' && ! $this->isQuoteExpired();
    }

    public function canCustomerCancel(): bool
    {
        return in_array($this->status, ['pending', 'quoted'], true);
    }

    public function hasQuote(): bool
    {
        return $this->quoted_total !== null;
    }
}

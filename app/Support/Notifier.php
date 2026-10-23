<?php

namespace App\Support;

use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends plain e-mail notifications. A mail server problem must never stop a
 * customer from submitting a request, so every failure is only logged.
 */
class Notifier
{
    public static function newRequest(QuoteRequest $quote): void
    {
        $admins = User::where('role', 'admin')->pluck('email')->all();
        $who = $quote->user->displayName();

        self::send($admins, "New quotation request {$quote->reference} from {$who}",
            "{$who} just submitted a request: {$quote->title}\n\nOpen it: ".route('admin.quotes.show', $quote));
    }

    public static function quoteReady(QuoteRequest $quote): void
    {
        $total = config('business.currency').' '.number_format($quote->quoted_total, 0);

        self::send([$quote->user->email], "Your quotation {$quote->reference} is ready",
            "Hello {$quote->user->name},\n\nYour quotation for \"{$quote->title}\" is ready: {$total}.\n"
            ."Please review and accept it here:\n".route('customer.quotes.show', $quote)
            ."\n\n".config('business.name'));
    }

    public static function customerResponded(QuoteRequest $quote): void
    {
        $admins = User::where('role', 'admin')->pluck('email')->all();

        self::send($admins, "{$quote->user->displayName()} {$quote->status} quotation {$quote->reference}",
            route('admin.quotes.show', $quote));
    }

    public static function statusChanged(QuoteRequest $quote): void
    {
        self::send([$quote->user->email], "Update on your order {$quote->reference}: {$quote->statusLabel()}",
            "Hello {$quote->user->name},\n\nThe status of \"{$quote->title}\" is now: {$quote->statusLabel()}.\n\n"
            .route('customer.quotes.show', $quote)."\n\n".config('business.name'));
    }

    public static function newMessage(QuoteRequest $quote, User $from, string $body): void
    {
        $to = $from->isAdmin() ? [$quote->user->email] : User::where('role', 'admin')->pluck('email')->all();
        $link = $from->isAdmin() ? route('customer.quotes.show', $quote) : route('admin.quotes.show', $quote);

        self::send($to, "New message on {$quote->reference}", "{$from->displayName()} wrote:\n\n{$body}\n\n{$link}");
    }

    private static function send(array $to, string $subject, string $body): void
    {
        $to = array_values(array_filter($to));
        if (! $to) {
            return;
        }

        try {
            Mail::raw($body, fn ($m) => $m->to($to)->subject($subject));
        } catch (\Throwable $e) {
            Log::warning('Mail failed: '.$e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $count = fn (array $statuses) => QuoteRequest::whereIn('status', $statuses)->count();

        return view('admin.dashboard', [
            'stats' => [
                'pending' => $count(['pending']),
                'quoted' => $count(['quoted']),
                'active' => $count(['accepted', 'in_production', 'ready']),
                'customers' => User::where('role', 'customer')->count(),
                'messages' => ContactMessage::whereNull('read_at')->count(),
                'revenue' => (float) QuoteRequest::whereIn('status', ['accepted', 'in_production', 'ready', 'completed'])->sum('quoted_total'),
            ],
            'pendingQuotes' => QuoteRequest::with('user')->withCount('items')->where('status', 'pending')->oldest()->limit(8)->get(),
            'recent' => QuoteRequest::with('user')->latest('updated_at')->limit(8)->get(),
        ]);
    }
}

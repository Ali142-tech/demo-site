<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $quotes = $user->quoteRequests();

        return view('customer.dashboard', [
            'stats' => [
                'total' => (clone $quotes)->count(),
                'waiting' => (clone $quotes)->where('status', 'pending')->count(),
                'ready' => (clone $quotes)->where('status', 'quoted')->count(),
                'active' => (clone $quotes)->whereIn('status', ['accepted', 'in_production', 'ready'])->count(),
                'done' => (clone $quotes)->where('status', 'completed')->count(),
            ],
            'needsAction' => (clone $quotes)->where('status', 'quoted')->latest()->get(),
            'recent' => (clone $quotes)->withCount('items')->latest()->limit(6)->get(),
        ]);
    }
}

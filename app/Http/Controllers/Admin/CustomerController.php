<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $type = $request->query('type');

        $customers = User::where('role', 'customer')
            ->withCount('quoteRequests')
            ->withSum(['quoteRequests as spent' => fn ($q) => $q->whereIn('status', ['accepted', 'in_production', 'ready', 'completed'])], 'quoted_total')
            ->when($type && isset(User::ACCOUNT_TYPES[$type]), fn ($q) => $q->where('account_type', $type))
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('organization', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers', 'search', 'type'));
    }

    public function show(User $customer)
    {
        abort_if($customer->isAdmin(), 404);

        return view('admin.customers.show', [
            'customer' => $customer,
            'quotes' => $customer->quoteRequests()->withCount('items')->latest()->get(),
        ]);
    }
}

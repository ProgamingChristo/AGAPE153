<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::query()
            ->where('role', 'member')
            ->withCount('orders')
            ->withSum('orders as total_spend', 'total_amount')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = (string) $request->input('q');
                $query->where(function ($inner) use ($term): void {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('company_name', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('orders_count')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        abort_unless($customer->role === 'member', 404);

        return view('admin.customers.show', [
            'customer' => $customer->load(['orders.items']),
        ]);
    }
}

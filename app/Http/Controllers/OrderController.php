<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search_str = $request->string('search_str')->toString();

        $orders = Order::query()
            ->with(['customer', 'products'])
            ->when($search_str, function ($query, $search) {
                $search_terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
                $query->whereHas('customer', function ($q) use ($search_terms) {
                    foreach ($search_terms as $term) {
                        $escaped_term = str_replace(['%', '_'], ['\\%', '\\_'], $term);
                        $q->where('name', 'LIKE', '%' . $escaped_term . '%');
                    }
                });
            })
            ->orderBy('orderday', 'desc')
            ->paginate(config('pagination.orders_per_page', 5))
            ->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'search_str' => $search_str,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::get(['id', 'name']);
        $products = Product::get(['id', 'name', 'code', 'price', 'tax']);

        return Inertia::render('Orders/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        DB::transaction(function () use ($request) {
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'orderday' => $request->orderday,
            ]);

            $products = collect($request->products)->mapWithKeys(function ($product) {
                return [$product['id'] => ['quantity' => $product['quantity']]];
            });

            $order->products()->attach($products);
        });

        return to_route('orders.index')
            ->with('success', '注文を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}

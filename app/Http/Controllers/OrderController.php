<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
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
        $products = Product::get(['id', 'name', 'code', 'price', 'tax']);

        return Inertia::render('Orders/Create', [
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
        $this->authorize('update', $order);

        $products = Product::get(['id', 'name', 'code', 'price', 'tax']);

        // 中間テーブルのquantityを含めてリレーションをロード
        $order->load(['customer', 'products']);

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'products' => $products,
        ]);
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

    /**
     * Search customers for combobox.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->input('query');
        $escaped_query = str_replace(['%', '_'], ['\\%', '\\_'], $query);
        $customers = Customer::where('name', 'like', '%' . $escaped_query . '%')
            ->limit(config('pagination.search_results_limit', 20)) // パフォーマンスのために結果を制限
            ->get(['id', 'name']);

        return response()->json($customers);
    }

    /**
     * Search products for combobox.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $escaped_query = str_replace(['%', '_'], ['\\%', '\\_'], $query);
        $products = Product::where('name', 'like', '%' . $escaped_query . '%')
            ->orWhere('code', 'like', '%' . $escaped_query . '%')
            ->limit(config('pagination.search_results_limit', 20)) // パフォーマンスのために結果を制限
            ->get(['id', 'name', 'code', 'price', 'tax']);

        return response()->json($products);
    }
}

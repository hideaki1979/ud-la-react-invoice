<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'orderday',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    protected $casts = [
        'orderday' => 'date:Y-m-d',
    ];

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute(): int
    {
        if (! $this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->reduce(function ($sum, $product) {
            $taxRate = 1 + $product->tax / 100;
            return $sum + floor($product->price * $product->pivot->quantity * $taxRate);
        }, 0);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'sku',
        'price',
        'purchase_price',
        'stock',
        'description',
        'category_id',
        'type',
        'unit',
        'location',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }
}

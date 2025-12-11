<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'unit_price',
        'supplier_id',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    /**
     * Relationships
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'shipment_products')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }
}

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
        'image',
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
     * Get the product image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        // Return a data URI SVG placeholder with the product's initials
        $initials = strtoupper(substr($this->name ?? 'P', 0, 2));
        return "data:image/svg+xml," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect fill="#E5E7EB" width="200" height="200" rx="12"/><text x="100" y="108" font-family="Arial,sans-serif" font-size="48" font-weight="bold" fill="#9CA3AF" text-anchor="middle">' . $initials . '</text></svg>');
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

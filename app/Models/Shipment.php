<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'type',
        'created_by_user_id',
        'customer_po',
        'scg_po',
        'booking_number',
        'delivery_note_number',
        'supplier_invoice',
        'status',
        'etd_port',
        'eta_port',
        'ata_port',
        'customer_receiving_schedule',
        'ata_customer',
        'shipping_cost',
        'customs_cost',
        'other_costs',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'etd_port' => 'date',
            'eta_port' => 'date',
            'ata_port' => 'date',
            'customer_receiving_schedule' => 'date',
            'ata_customer' => 'date',
            'shipping_cost' => 'decimal:2',
            'customs_cost' => 'decimal:2',
            'other_costs' => 'decimal:2',
        ];
    }

    /**
     * Relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'shipment_products')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Accessors for OTD Logic
     */
    public function isDelivered(): bool
    {
        return $this->status === 'Delivered';
    }

    /**
     * Check if shipment is On-Time
     * OTD Definition: ata_customer <= customer_receiving_schedule
     */
    public function isOnTime(): bool
    {
        if (!$this->isDelivered() || !$this->ata_customer || !$this->customer_receiving_schedule) {
            return false;
        }

        return $this->ata_customer->lte($this->customer_receiving_schedule);
    }

    /**
     * Check if shipment is Late
     */
    public function isLate(): bool
    {
        if (!$this->isDelivered() || !$this->ata_customer || !$this->customer_receiving_schedule) {
            return false;
        }

        return $this->ata_customer->gt($this->customer_receiving_schedule);
    }

    /**
     * Get days until deadline (negative if overdue)
     */
    public function daysUntilDeadline(): ?int
    {
        if (!$this->customer_receiving_schedule) {
            return null;
        }

        $deadline = $this->customer_receiving_schedule;
        $compareDate = $this->ata_customer ?? Carbon::today();

        return $compareDate->diffInDays($deadline, false);
    }

    /**
     * Get total cost
     */
    public function getTotalCostAttribute(): float
    {
        return (float) ($this->shipping_cost + $this->customs_cost + $this->other_costs);
    }

    /**
     * Query Scopes
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'Delivered');
    }

    public function scopeOnTime($query)
    {
        return $query->delivered()
            ->whereNotNull('ata_customer')
            ->whereNotNull('customer_receiving_schedule')
            ->whereRaw('ata_customer <= customer_receiving_schedule');
    }

    public function scopeLate($query)
    {
        return $query->delivered()
            ->whereNotNull('ata_customer')
            ->whereNotNull('customer_receiving_schedule')
            ->whereRaw('ata_customer > customer_receiving_schedule');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'In Transit');
    }
}

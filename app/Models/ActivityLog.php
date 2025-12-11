<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipment_id',
        'action',
        'old_value',
        'new_value',
        'description',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Helper method to create activity log
     */
    public static function logActivity(int $userId, int $shipmentId, string $action, ?string $oldValue = null, ?string $newValue = null, ?string $description = null)
    {
        return self::create([
            'user_id' => $userId,
            'shipment_id' => $shipmentId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
        ]);
    }
}

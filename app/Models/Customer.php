<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact_person',
        'phone',
        'email',
        'pic_user_id',
    ];

    /**
     * Relationships
     */
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}

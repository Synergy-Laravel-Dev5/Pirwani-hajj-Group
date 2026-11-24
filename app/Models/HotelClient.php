<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel_clients';

    protected $guarded = ['id'];

    public function sales()
    {
        return $this->hasMany(HotelSale::class, 'hotel_client_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->company_name) && !empty($this->name)) {
            return "{$this->company_name} — {$this->name}";
        }
        return $this->company_name ?: ($this->name ?: 'Client #' . $this->id);
    }
}

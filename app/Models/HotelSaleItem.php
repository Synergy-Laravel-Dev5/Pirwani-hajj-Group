<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelSaleItem extends Model
{
    use HasFactory;

    protected $table = 'hotel_sale_items';

    protected $guarded = ['id'];

    protected $casts = [
        'check_in'      => 'date',
        'check_out'     => 'date',
        'quantity'      => 'integer',
        'male_beds'     => 'integer',
        'female_beds'   => 'integer',
        'no_of_nights'  => 'integer',
        'rate_per_unit' => 'decimal:2',
        'amount'        => 'decimal:2',
    ];

    public function hotelSale()
    {
        return $this->belongsTo(HotelSale::class, 'hotel_sale_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}

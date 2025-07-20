<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Car extends Model
{
    protected $fillable = [
        'handle',
        'title',
        'body_html',
        'gallery',
        'status',
        'variant_price',
        'condition',
        'brand',
    ];


    public function brand(){
        return $this->belongsTo(Brand::class, "brand", "brand_name");
    }
}

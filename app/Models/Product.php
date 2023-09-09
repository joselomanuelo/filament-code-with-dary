<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = array(
        'brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'image',
        'quantity',
        'price',
        'is_visible',
        'is_featured',
        'type',
        'publish_at',
    );
}

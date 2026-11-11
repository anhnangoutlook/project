<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutOfStock extends Model
{
    use HasFactory;

    protected $table = 'out_of_stock';

    protected $fillable = [
        'product_id',
        'category',
        'name',
        'code_from',
        'code_to',
        'total_qty',
        'removed_qty',
        'remain_qty',
        'total_weight',
        'removed_weight',
        'remain_weight',
        'store_id',
        'threshold',
    ];
}

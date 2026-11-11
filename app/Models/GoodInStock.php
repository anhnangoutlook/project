<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodInStock extends Model
{
    use HasFactory;

    protected $table = 'good_in_stock';

    protected $fillable = ['code', 'name', 'weight', 'qty1', 'imported_at'];
}

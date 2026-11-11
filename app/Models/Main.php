<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $table = 'main';

    protected $fillable = ['category', 'name', 'code'];
}

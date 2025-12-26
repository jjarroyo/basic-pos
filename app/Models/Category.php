<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'color', 'is_active'];

    // Relación futura con productos
    // public function products() { return $this->hasMany(Product::class); }
}

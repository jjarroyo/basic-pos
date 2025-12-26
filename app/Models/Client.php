<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'identification', 'document_type', 
        'email', 'phone', 'address', 'is_active'
    ];
}
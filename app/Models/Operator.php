<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use SoftDeletes;
    protected $table = 'operators';
    
    protected $fillable = [
        'name',
        'website_url',
        'logo_url',
    ];
}

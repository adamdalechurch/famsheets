<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DebugText extends Model
{
    use HasFactory;

   protected $table = 'debug_text';

    protected $fillable = [
        'text',
    ];
}
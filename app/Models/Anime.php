<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    use HasFactory;

    protected $fillable = [
        'mal_id',  // Allow mass assignment for this field
        'titles',
        'slug',
        'synopsis',
    ];
}

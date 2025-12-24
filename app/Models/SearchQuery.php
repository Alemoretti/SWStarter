<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'type',
        'results_count',
        'response_time_ms',
    ];

    protected $casts = [
        'results_count' => 'integer',
        'response_time_ms' => 'integer',
    ];
}
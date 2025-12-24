<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'top_queries',
        'avg_response_time',
        'popular_hour',
        'computed_at',
    ];

    protected $casts = [
        'top_queries' => 'array',
        'avg_response_time' => 'decimal:2',
        'popular_hour' => 'integer',
        'computed_at' => 'datetime',
    ];
}
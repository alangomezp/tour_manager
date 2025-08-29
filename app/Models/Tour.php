<?php

namespace App\Models;

use Date;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tour extends Model
{
    /** @use HasFactory<\Database\Factories\TourFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'available_spaces',
        'total_spaces',
        'date'
    ];

    public static function filterByPrice(int $min, int $max): Collection
    {
        return static::where('price', '>=', $min)
            ->where('price', '<=', $max)->get();
    }

    public static function filterByDates($date_min, $date_max): Collection
    {
        return static::where('date', '>=', $date_min)
            ->where('date', '<=', $date_max)->get();
    }
}

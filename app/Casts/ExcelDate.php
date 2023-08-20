<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ExcelDate implements CastsAttributes
{
    /**
     * Каст формата к d.m.Y при получении даты из бд
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d.m.Y');
    }

    /**
     * Каст к Y-m-d при записи даты в бд
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Carbon::createFromFormat('d.m.y', $value)->format('Y-m-d');
    }
}

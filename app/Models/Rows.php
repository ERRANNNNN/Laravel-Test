<?php

namespace App\Models;

use App\Casts\ExcelDate;
use Illuminate\Database\Eloquent\Model;

class Rows extends Model
{
    public $timestamps = false;

    protected $fillable = ['excel_id', 'name', 'date'];

    protected $casts = [
        'date' => ExcelDate::class,
    ];
}

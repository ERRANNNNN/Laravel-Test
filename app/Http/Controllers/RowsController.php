<?php

namespace App\Http\Controllers;

use App\Models\Rows;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class RowsController extends Controller
{
    /**
     * Отображение импортированных строк из Excel
     * @return \Illuminate\Foundation\Application|View|Factory|Application
     */
    public function index(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        $rows = Rows::all();
        return view('rows')->with('rows', $rows);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Rows;

class RowsController extends Controller
{
    public function index()
    {
        $rows = Rows::all();
        return view('rows')->with('rows', $rows);
    }
}

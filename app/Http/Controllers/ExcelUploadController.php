<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessExcelParsing;
use App\Services\ExcelService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class ExcelUploadController extends Controller
{
    /**
     * Отображение формы загрузки файла
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function create(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return view('excel_upload');
    }

    /**
     * Загрузка файла excel
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        Validator::validate($request->all(), [
            'excel' => [
                'required',
                File::types(['xls', 'xlsx'])
            ]
        ]);
        $file = $request->file('excel');
        $path = Storage::path($file->storeAs('', 'input.' . $file->getClientOriginalExtension()));

        $excelService = new ExcelService($path);

        Cache::set('excel_total_rows', $excelService->getTotalRows('A'));

        dispatch(new ProcessExcelParsing($path));

        return Redirect::to(route('rows'));
    }
}

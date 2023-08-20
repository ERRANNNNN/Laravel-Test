<?php

namespace App\Jobs;

use App\Events\RowsUpdated;
use App\Models\Rows;
use App\Services\ExcelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Exception;

class ProcessExcelParsing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ExcelService $excelService;
    protected string $filePath;

    protected int $sheetFirstRow = 2;

    protected int $rowsPerIteration = 1000;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->excelService = new ExcelService($this->filePath);
        $currentRowsProcessed = Cache::get('excel_rows_processed', $this->sheetFirstRow);
        $totalRows = Cache::get('excel_total_rows', $this->excelService->getTotalRows('A'));
        $colsNames = [
            'A' => 'excel_id',
            'B' => 'name',
            'C' => 'date'
        ];
        Log::info('CurrenRowsProcessed ' . $currentRowsProcessed);
        $data = $this->excelService
            ->getFormattedData($currentRowsProcessed, $colsNames, $this->rowsPerIteration);
        $data = (new Collection($data))->map(function ($row) {
            $rows = Rows::create(['excel_id' => $row['excel_id'], 'name' => $row['name'], 'date' => $row['date']]);
            broadcast(new RowsUpdated($rows));
            return $rows;
        });


        Log::info('data ' . json_encode($data));


        $newRowsProcessed = $currentRowsProcessed + count($data);
        if ($totalRows > $newRowsProcessed) {
            Cache::set('excel_rows_processed', $newRowsProcessed);
            dispatch(new ProcessExcelParsing($this->filePath));
        } else {
            Cache::delete('excel_total_rows');
            Cache::delete('excel_rows_processed');
        }
    }
}

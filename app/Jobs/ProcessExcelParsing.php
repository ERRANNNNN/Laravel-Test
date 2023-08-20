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

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        //Инициализируем все необходимые данные
        $this->excelService = new ExcelService($this->filePath);
        $currentRowsProcessed = Cache::get('excel_rows_processed', $this->sheetFirstRow);
        $totalRows = Cache::get('excel_total_rows', $this->excelService->getTotalRows('A'));
        $colsNames = [
            'A' => 'excel_id',
            'B' => 'name',
            'C' => 'date'
        ];

        //Получим данные из excel
        $data = $this->excelService
            ->getFormattedData($currentRowsProcessed, $colsNames, $this->rowsPerIteration);

        //Создадим записи в таблице rows и отправим события о создании записей
        $data = (new Collection($data))->each(function ($row) {
            $rows = Rows::create(['excel_id' => $row['excel_id'], 'name' => $row['name'], 'date' => $row['date']]);
            broadcast(new RowsUpdated($rows));
        });

        $newRowsProcessed = $currentRowsProcessed + count($data);

        //Если процесс парсинга не завершен, то вызываем Job ещё раз, в случае завершения очищаем кэш
        if ($totalRows > $newRowsProcessed) {
            Cache::set('excel_rows_processed', $newRowsProcessed);
            dispatch(new ProcessExcelParsing($this->filePath));
        } else {
            Cache::delete('excel_total_rows');
            Cache::delete('excel_rows_processed');
        }
    }
}

<?php

namespace App\Services;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelService
{
    protected Spreadsheet $spreadsheet;
    protected Cells $cells;

    /**
     * @param string $spreadsheetPath
     */
    public function __construct(string $spreadsheetPath)
    {
        $this->spreadsheet = IOFactory::load($spreadsheetPath);
        $this->cells = $this->spreadsheet->getActiveSheet()->getCellCollection();
    }

    /**
     * Получение количества строк в столбце
     * @param string $column
     * @return int
     */
    public function getTotalRows(string $column): int
    {
        return $this->cells->getHighestRow($column);
    }

    /**
     * Получение форматированных данных
     * @param int $fromRow - первая строка
     * @param array $colsNames - ['A' => 'Имя в результирующем массиве', ...]
     * @param int $rowsCount - количество получаемых строк
     * @return array
     * @throws Exception
     */
    public function getFormattedData(int $fromRow, array $colsNames, int $rowsCount): array
    {
        $toRow = $fromRow + $rowsCount - 1;
        $resultRowsData = [];
        for ($row = $fromRow; $row <= $toRow; $row++)
        {
            $rowData = [];
            for ($col = 'A'; $col <= $this->cells->getHighestColumn(); $col++) {
                $cell = $this->cells->get($col . $row);
                if ($cell !== null && key_exists($col, $colsNames)) {
                    $rowData[$colsNames[$col]] = $cell->getFormattedValue();
                } else break;
            }
            if (empty($rowData)) {
                break;
            }
            $resultRowsData[] = $rowData;
        }
        return $resultRowsData;
    }
}

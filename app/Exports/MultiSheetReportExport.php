<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetReportExport implements WithMultipleSheets
{
    private array $sheetDefinitions;

    public function __construct(array $sheetDefinitions)
    {
        $this->sheetDefinitions = $sheetDefinitions;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->sheetDefinitions as $definition) {
            $sheets[] = new StyledReportExport(
                $definition['title'],
                $definition['columns'],
                $definition['rows']
            );
        }

        return $sheets;
    }
}

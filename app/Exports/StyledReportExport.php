<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StyledReportExport implements FromArray, WithHeadings, WithEvents, WithTitle, WithCustomStartCell
{
    private string $title;

    private array $columns;

    private array $headings;

    private array $rows;

    public function __construct(string $title, array $columns, array $rows)
    {
        $this->title = $title;
        $this->columns = $columns;
        $this->headings = array_map([$this, 'toHeadingLabel'], $columns);
        $this->rows = array_map(function (array $row) use ($columns) {
            $normalized = [];
            foreach ($columns as $column) {
                $value = $row[$column] ?? '';
                $normalized[] = $value === null ? '' : $value;
            }

            return $normalized;
        }, $rows);
    }

    public function title(): string
    {
        return substr($this->title, 0, 31);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = Coordinate::stringFromColumnIndex(count($this->columns));
                $headerRow = 2;
                $dataStartRow = 3;
                $lastRow = max(count($this->rows) + 2, 2);
                $titleRange = "A1:{$lastColumn}1";
                $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
                $fullRange = "A{$headerRow}:{$lastColumn}{$lastRow}";

                $sheet->mergeCells($titleRange);
                $sheet->setCellValue('A1', $this->title . ' Export');
                $sheet->getStyle($titleRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '111827'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'EEF2FF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                $sheet->freezePane('A3');
                $sheet->setAutoFilter($headerRange);

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                $sheet->getStyle($fullRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);

                if ($lastRow >= $dataStartRow) {
                    for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                        if ($row % 2 === 0) {
                            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()->applyFromArray([
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => 'F9FAFB'],
                            ]);
                        }
                    }
                }

                foreach ($this->columns as $index => $column) {
                    $columnIndex = $index + 1;
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $dataEndRow = max($lastRow, $dataStartRow);
                    $range = "{$columnLetter}{$dataStartRow}:{$columnLetter}{$dataEndRow}";

                    $sheet->getColumnDimension($columnLetter)->setWidth($this->calculateColumnWidth($index));
                    $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($range)->getAlignment()->setWrapText(false);

                    if ($this->isDateColumn($column)) {
                        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } elseif ($this->isNumericColumn($column)) {
                        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    } elseif ($this->isBooleanColumn($column)) {
                        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } else {
                        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }

                    $sheet->getStyle($range)->getNumberFormat()->setFormatCode($this->numberFormatForColumn($column));
                }
            },
        ];
    }

    private function toHeadingLabel(string $column): string
    {
        $label = str_replace('_', ' ', $column);
        $label = ucwords($label);

        return str_replace(
            [' Id', ' Sku', ' Utc', ' Ltv', ' Aov', ' Crm'],
            [' ID', ' SKU', ' UTC', ' LTV', ' AOV', ' CRM'],
            $label
        );
    }

    private function calculateColumnWidth(int $columnIndex): float
    {
        $maxLength = strlen($this->headings[$columnIndex] ?? '');

        foreach ($this->rows as $row) {
            $length = strlen((string) ($row[$columnIndex] ?? ''));
            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        return max(4, $maxLength + 6);
    }

    private function isDateColumn(string $column): bool
    {
        return str_contains($column, '_at_')
            || str_ends_with($column, '_at')
            || str_ends_with($column, '_date')
            || str_ends_with($column, '_month');
    }

    private function isNumericColumn(string $column): bool
    {
        if (str_ends_with($column, '_id')) {
            return false;
        }

        foreach ([
            'amount',
            'price',
            'cost',
            'value',
            'qty',
            'quantity',
            'orders',
            'units',
            'days',
            'frequency',
            'score',
            'rate',
            'margin',
            'turnover',
            'count',
        ] as $token) {
            if (str_contains($column, $token)) {
                return true;
            }
        }

        return false;
    }

    private function isBooleanColumn(string $column): bool
    {
        return str_contains($column, '_opt_in');
    }

    private function numberFormatForColumn(string $column): string
    {
        if ($this->isDateColumn($column)) {
            if (str_ends_with($column, '_month')) {
                return 'yyyy-mm';
            }
            if (str_ends_with($column, '_date')) {
                return 'yyyy-mm-dd';
            }

            return 'yyyy-mm-dd hh:mm:ss';
        }

        if (!$this->isNumericColumn($column)) {
            return NumberFormat::FORMAT_GENERAL;
        }

        foreach (['rate', 'score', 'turnover', 'frequency'] as $token) {
            if (str_contains($column, $token)) {
                return '0.0000';
            }
        }

        foreach (['days_of_cover'] as $token) {
            if (str_contains($column, $token)) {
                return '0.00';
            }
        }

        foreach (['qty', 'quantity', 'orders', 'units', 'days', 'count'] as $token) {
            if (str_contains($column, $token)) {
                return '#,##0';
            }
        }

        return '#,##0.00';
    }
}

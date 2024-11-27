<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ListComplaineeController extends Controller
{
    private function getComplaineeData()
    {
        $client = new \MongoDB\Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
    
        $reports = $reportCollection->find()->toArray();
    
        $complaineeData = [];
        foreach ($reports as $report) {
            if (empty($report->idNumber)) {
                continue; 
            }
    
            $idNumber = $report->idNumber;
            if (!isset($complaineeData[$idNumber])) {
                $complaineeData[$idNumber] = [
                    'Complainee\'s Name' => $report->perpetratorName ?? 'N/A',
                    'ID Number' => $idNumber,
                    'Grade/Year Level' => $report->perpetratorGradeYearLevel ?? 'N/A',
                    'Role' => $report->perpetratorRole ?? 'N/A',
                    'Incident Count' => 0,
                ];
            }
            $complaineeData[$idNumber]['Incident Count']++;
        }
    
        return array_values($complaineeData); 
    }
    

    public function exportComplaineesCSV()
    {
        $data = $this->getComplaineeData();

        if (empty($data)) {
            return redirect()->back()->with('error', 'No data available for export.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = array_keys($data[0]);
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Populate data
        $row = 2;
        foreach ($data as $record) {
            $column = 'A';
            foreach ($record as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }

        $filename = 'complainee_report_' . date('Y-m-d_His') . '.csv';
        $writer = new Csv($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function exportComplaineesXLSX()
    {
        $data = $this->getComplaineeData();

        if (empty($data)) {
            return redirect()->back()->with('error', 'No data available for export.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Apply styles
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set headers
        $headers = array_keys($data[0]);
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        $lastColumn = $sheet->getHighestColumn();
        $headerRange = 'A1:' . $lastColumn . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '164789'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Populate data
        $row = 2;
        foreach ($data as $record) {
            $column = 'A';
            foreach ($record as $value) {
                $sheet->setCellValue($column . $row, $value);
                $sheet->getStyle($column . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                ]);
                $column++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $lastColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'complainee_report_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}

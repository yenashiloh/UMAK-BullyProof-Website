<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ReportExportController extends Controller
{
    private function getReportData()
    {
        $client = new \MongoDB\Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $userCollection = $client->bullyproof->users;
        
        $reports = $reportCollection->find([])->toArray();
        
        $formattedReports = [];
        foreach ($reports as $report) {
            $reporter = $userCollection->findOne(['_id' => $report->reportedBy]);
            
            $platformUsed = [];
            if (isset($report->platformUsed)) {
                if ($report->platformUsed instanceof \MongoDB\Model\BSONArray) {
                    $platformUsed = $report->platformUsed->getArrayCopy();
                } elseif (is_array($report->platformUsed)) {
                    $platformUsed = $report->platformUsed;
                }
            }

            $cyberbullyingType = [];
            if (isset($report->cyberbullyingType)) {
                if ($report->cyberbullyingType instanceof \MongoDB\Model\BSONArray) {
                    $cyberbullyingType = $report->cyberbullyingType->getArrayCopy();
                } elseif (is_array($report->cyberbullyingType)) {
                    $cyberbullyingType = $report->cyberbullyingType;
                }
            }

            $date = $report->reportDate->toDateTime();
            $date->setTimezone(new \DateTimeZone('Asia/Manila'));
            
            $formattedReports[] = [
                'Report Date' => $date->format('F j, Y, g:iA'),
                'Reporter Name' => $reporter->fullname ?? 'N/A',
                'Reporter Email' => $reporter->email ?? 'N/A',
                'Victim Name' => $report->victimName ?? 'N/A',
                'Victim Type' => $report->victimType ?? 'N/A',
                'Victim Relationship' => $report->victimRelationship ?? 'N/A',
                'Grade/Year Level' => $report->gradeYearLevel ?? 'N/A',
                'Platform Used' => !empty($platformUsed) ? implode(', ', $platformUsed) : 'N/A',
                'Cyberbullying Type' => !empty($cyberbullyingType) ? implode(', ', $cyberbullyingType) : 'N/A',
                'Incident Details' => $report->incidentDetails ?? 'N/A',
                'Perpetrator Name' => $report->perpetratorName ?? 'N/A',
                'Perpetrator Role' => $report->perpetratorRole ?? 'N/A',
                'Perpetrator Grade/Year' => $report->perpetratorGradeYearLevel ?? 'N/A',
                'Actions Taken' => $report->actionsTaken ?? 'N/A',
                'Actions Description' => $report->describeActions ?? 'N/A',
            ];
        }
        
        return $formattedReports;
    }

    public function exportCSV()
    {
        $reports = $this->getReportData();

        if (empty($reports)) {
            return redirect()->back()->with('error', 'No reports found to export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = array_keys($reports[0]);
        $column = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        $row = 2;
        foreach ($reports as $report) {
            $column = 'A';
            foreach ($report as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }

        $filename = 'incident_reports_' . date('Y-m-d_His') . '.csv';
        $writer = new Csv($spreadsheet);

        return response()->stream(
            function() use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }


    public function exportXLSX()
    {
        $reports = $this->getReportData();
        
        if (empty($reports)) {
            return redirect()->back()->with('error', 'No reports found to export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        
        $headers = array_keys($reports[0]);
        $column = 'A';
        $incidentDetailsColumn = null;
        $platformUsedColumn = null;
        $cyberbullyingTypeColumn = null;
        
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            if ($header === 'Incident Details') {
                $incidentDetailsColumn = $column;
            } elseif ($header === 'Platform Used') {
                $platformUsedColumn = $column;
            } elseif ($header === 'Cyberbullying Type') {
                $cyberbullyingTypeColumn = $column;
            }
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
                'startColor' => ['rgb' => '4B5563'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        $row = 2;
        foreach ($reports as $report) {
            $column = 'A';
            foreach ($report as $key => $value) {
                $cell = $column . $row;
                
                if (in_array($key, ['Incident Details', 'Platform Used', 'Cyberbullying Type'])) {
                    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
                    $sheet->getStyle($cell)->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'wrapText' => true,
                            'shrinkToFit' => false
                        ]
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                } else {
                    $sheet->setCellValue($cell, $value);
                }
                
                $sheet->getStyle($cell)->applyFromArray([
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
        
        foreach (range('A', $lastColumn) as $columnID) {
            if ($columnID === $incidentDetailsColumn) {
                $sheet->getColumnDimension($columnID)->setWidth(50);
            } elseif ($columnID === $platformUsedColumn || $columnID === $cyberbullyingTypeColumn) {
                $sheet->getColumnDimension($columnID)->setWidth(30); 
            } else {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        }
        
        $dataRange = 'A2:' . $lastColumn . $row;
        $sheet->getStyle($dataRange)->applyFromArray([
            'font' => [
                'size' => 11,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        $sheet->freezePane('A2');
        
        $filename = 'incident_reports_' . date('Y-m-d_His') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        return response()->stream(
            function() use ($writer) {
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
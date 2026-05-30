<?php

namespace App\Helpers;

class CsvExporter
{
    public static function export($data, $filename, $columns)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add header row
            fputcsv($file, array_values($columns));

            foreach ($data as $row) {
                $line = [];
                foreach (array_keys($columns) as $column) {
                    $value = data_get($row, $column);
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    $line[] = $value;
                }
                fputcsv($file, $line);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

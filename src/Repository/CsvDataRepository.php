<?php

namespace App\Repository;

class CsvDataRepository
{
    public function convertCsvToJson(string $csvPath, int $maxRows = 10000): array
    {
        ini_set('memory_limit', '256M');

        if (!file_exists($csvPath) || !is_readable($csvPath)) {
            return ['error' => 'File not found or not readable'];
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return ['error' => 'Failed to open CSV file'];
        }

        $headers = fgetcsv($handle, 0, ";");
        if ($headers === false) {
            fclose($handle);
            return ['error' => 'Failed to read CSV headers'];
        }

        $data = [];
        $rowCount = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false && $rowCount < $maxRows) {
            $rowData = [];
            foreach ($headers as $index => $header) {
                $value = $row[$index] ?? null;
                $rowData[$header] = $value !== '' ? $value : 'pas de données dans la row';
            }
            $data[] = $rowData;
            $rowCount++;
        }

        fclose($handle);

        $dirPath = __DIR__ . '/../../public/data';
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $jsonFilePath = $dirPath . '/result.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($jsonFilePath, $json);

        return $data;
    }

    public function addDataToCsv(array $newData): bool
    {
        $csvPath = '/public/gaz.csv';

        $handle = fopen($csvPath, 'a');
        if (!$handle) {
            return false;
        }

        if (fputcsv($handle, $newData) === false) {
            fclose($handle);
            return false;
        }

        fclose($handle);
        return true;
    }

    public function deleteDataFromCsv(string $identifier): bool
    {
        $csvPath = '/public/gaz.csv';

        $data = $this->readCsvFile($csvPath);

        $filteredData = array_filter($data, function($row) use ($identifier) {
            return $row['cle_primaire'] !== $identifier;
        });

        if (!$this->rewriteCsvFile($csvPath, $filteredData)) {
            return false;
        }

        return true;
    }

    public function updateDataInCsv(string $identifier, array $updatedData): bool
    {
        $csvPath = '/public/gaz.csv';

        $data = $this->readCsvFile($csvPath);

        foreach ($data as &$row) {
            if ($row['cle_primaire'] === $identifier) {
                $row['colonne_modifiable'] = $updatedData['colonne_modifiable'];
            }
        }

        if (!$this->rewriteCsvFile($csvPath, $data)) {
            return false;
        }

        return true;
    }

    private function readCsvFile(string $csvPath): array
    {
        $data = [];
    
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return $data;
        }
    
        $headers = fgetcsv($handle, 0, ";");
        if ($headers === false) {
            fclose($handle);
            return $data;
        }
    
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowData = [];
            foreach ($headers as $index => $header) {
                $value = $row[$index] ?? null;
                $rowData[$header] = $value !== '' ? $value : 'pas de données dans la row';
            }
            $data[] = $rowData;
        }
    
        fclose($handle);
    
        return $data;
    }

    private function rewriteCsvFile(string $csvPath, array $data): bool
    {
        $handle = fopen($csvPath, 'w');
        if (!$handle) {
            return false;
        }
    
        fputcsv($handle, array_keys($data[0]), ';');
    
        foreach ($data as $rowData) {
            fputcsv($handle, $rowData, ';');
        }
    
        fclose($handle);
    
        return true;
    }
}

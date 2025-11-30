<?php

namespace App\Helpers;

class CsvHelper
{
    /**
     * Detect CSV delimiter automatically
     * Supports: comma (,), semicolon (;), tab (\t), pipe (|)
     * 
     * @param string $filePath Path to CSV file
     * @param int $checkLines Number of lines to check for detection
     * @return string Detected delimiter
     */
    public static function detectDelimiter($filePath, $checkLines = 5)
    {
        $delimiters = [
            ';' => 0,  // Semicolon
            ',' => 0,  // Comma
            "\t" => 0, // Tab
            '|' => 0   // Pipe
        ];
        
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            return ','; // Default to comma if file cannot be opened
        }
        
        // Check first few lines to detect delimiter
        $lineCount = 0;
        while (($line = fgets($handle)) !== false && $lineCount < $checkLines) {
            foreach ($delimiters as $delimiter => $count) {
                $delimiters[$delimiter] += substr_count($line, $delimiter);
            }
            $lineCount++;
        }
        
        fclose($handle);
        
        // Return delimiter with highest count
        $detectedDelimiter = array_search(max($delimiters), $delimiters);
        
        // If no delimiter found or all counts are 0, default to comma
        return $detectedDelimiter !== false && max($delimiters) > 0 ? $detectedDelimiter : ',';
    }

    /**
     * Get delimiter name for display
     * 
     * @param string $delimiter
     * @return string
     */
    public static function getDelimiterName($delimiter)
    {
        $names = [
            ',' => 'Comma (,)',
            ';' => 'Semicolon (;)',
            "\t" => 'Tab',
            '|' => 'Pipe (|)'
        ];
        
        return $names[$delimiter] ?? 'Unknown';
    }

    /**
     * Read CSV file with auto-detected delimiter
     * 
     * @param string $filePath Path to CSV file
     * @param bool $skipHeader Whether to skip the first row (header)
     * @param callable|null $callback Callback function to process each row
     * @return array Array of rows or processed data
     */
    public static function readCsv($filePath, $skipHeader = true, $callback = null)
    {
        $delimiter = self::detectDelimiter($filePath);
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new \Exception('Cannot open CSV file');
        }
        
        $data = [];
        $rowNumber = 0;
        
        // Skip header if needed
        if ($skipHeader) {
            fgetcsv($handle, 0, $delimiter);
        }
        
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;
            
            if ($callback && is_callable($callback)) {
                $result = $callback($row, $rowNumber, $delimiter);
                if ($result !== null) {
                    $data[] = $result;
                }
            } else {
                $data[] = $row;
            }
        }
        
        fclose($handle);
        
        return [
            'data' => $data,
            'delimiter' => $delimiter,
            'delimiter_name' => self::getDelimiterName($delimiter),
            'total_rows' => $rowNumber
        ];
    }

    /**
     * Validate CSV structure
     * 
     * @param string $filePath Path to CSV file
     * @param int $expectedColumns Expected number of columns
     * @return array Validation result
     */
    public static function validateCsv($filePath, $expectedColumns = null)
    {
        $delimiter = self::detectDelimiter($filePath);
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            return [
                'valid' => false,
                'error' => 'Cannot open CSV file'
            ];
        }
        
        $header = fgetcsv($handle, 0, $delimiter);
        $columnCount = count($header);
        
        fclose($handle);
        
        if ($expectedColumns !== null && $columnCount !== $expectedColumns) {
            return [
                'valid' => false,
                'error' => "Expected {$expectedColumns} columns, found {$columnCount}",
                'column_count' => $columnCount,
                'delimiter' => $delimiter
            ];
        }
        
        return [
            'valid' => true,
            'column_count' => $columnCount,
            'delimiter' => $delimiter,
            'delimiter_name' => self::getDelimiterName($delimiter),
            'header' => $header
        ];
    }
}

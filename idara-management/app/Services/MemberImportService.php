<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use ZipArchive;

class MemberImportService
{
    /**
     * Parse and import members from an Excel (.xlsx) or CSV (.csv, .txt, .tsv) file.
     *
     * @return array{total: int, new: int, existing: int, errors: array<string>}
     */
    public function import(UploadedFile $file, Department $department, string $defaultRole = 'member', ?User $actor = null): array
    {
        $rows = $this->extractRows($file);

        if (empty($rows)) {
            return [
                'total' => 0,
                'new' => 0,
                'existing' => 0,
                'errors' => ['Faili halina taarifa au muundo hauwezi kusomeka.'],
            ];
        }

        // Detect column indices from header
        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($rows));
        $nameCol = $this->findColumnIndex($headers, ['jina', 'name', 'jina kamili', 'full name', 'mwanachama']);
        $phoneCol = $this->findColumnIndex($headers, ['simu', 'phone', 'namba ya simu', 'namba', 'mobile', 'telephone']);
        $emailCol = $this->findColumnIndex($headers, ['email', 'barua pepe', 'pepe', 'mail']);

        // If no headers matched, assume column 0 is Name, column 1 is Phone, column 2 is Email
        if ($nameCol === null) {
            $nameCol = 0;
            $phoneCol = 1;
            $emailCol = 2;
        }

        $newCount = 0;
        $existingCount = 0;
        $errors = [];

        DB::transaction(function () use ($rows, $nameCol, $phoneCol, $emailCol, $department, $defaultRole, $actor, &$newCount, &$existingCount, &$errors) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // account for header line

                $name = isset($row[$nameCol]) ? trim((string) $row[$nameCol]) : '';
                $phone = ($phoneCol !== null && isset($row[$phoneCol])) ? trim((string) $row[$phoneCol]) : null;
                $email = ($emailCol !== null && isset($row[$emailCol])) ? trim((string) $row[$emailCol]) : null;

                // Clean phone number
                if ($phone !== null && $phone !== '') {
                    $phone = preg_replace('/[^0-9+]/', '', $phone);
                } else {
                    $phone = null;
                }

                // If row is completely empty, skip
                if (empty($name) && empty($phone) && empty($email)) {
                    continue;
                }

                if (empty($name)) {
                    $errors[] = "Mstari wa {$rowNumber}: Jina halikuwekwa.";
                    continue;
                }

                // Find existing user by email or phone
                $user = null;
                if (! empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $user = User::where('email', $email)->first();
                }

                if (! $user && ! empty($phone)) {
                    $user = User::where('phone', $phone)->first();
                }

                if ($user) {
                    $existingCount++;
                    // Update phone/name if missing
                    if (empty($user->phone) && ! empty($phone)) {
                        $user->phone = $phone;
                        $user->save();
                    }
                } else {
                    // Generate email if not provided or invalid
                    if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $cleanSlug = Str::slug($name);
                        if (empty($cleanSlug)) {
                            $cleanSlug = 'mwanachama';
                        }
                        $email = $cleanSlug . '.' . ($phone ? substr($phone, -4) : rand(1000, 9999)) . '@kanisa.local';

                        // Ensure unique email
                        while (User::where('email', $email)->exists()) {
                            $email = $cleanSlug . '.' . rand(10000, 99999) . '@kanisa.local';
                        }
                    }

                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'password' => Hash::make('password'),
                        'is_active' => true,
                    ]);

                    $newCount++;
                }

                // Attach to department
                $department->users()->syncWithoutDetaching([
                    $user->id => ['role' => $defaultRole],
                ]);

                $user->syncGlobalRoleFromDepartments();
            }

            // Log activity
            if (($newCount + $existingCount) > 0) {
                ActivityLog::create([
                    'department_id' => $department->id,
                    'recorded_by' => $actor?->id ?? 1,
                    'title' => "Wanachama " . ($newCount + $existingCount) . " wameingizwa kwa Excel/CSV",
                    'description' => "Wanachama wapya {$newCount}, waliokuwepo {$existingCount}.",
                    'occurred_at' => now(),
                ]);
            }
        });

        return [
            'total' => $newCount + $existingCount,
            'new' => $newCount,
            'existing' => $existingCount,
            'errors' => $errors,
        ];
    }

    /**
     * Extract raw rows from .xlsx, .csv, .tsv, or .txt file.
     *
     * @return array<int, array<int, string>>
     */
    protected function extractRows(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            return $this->parseXlsx($file->getRealPath());
        }

        return $this->parseCsv($file->getRealPath());
    }

    /**
     * Parse standard CSV/TSV file with delimiter autodetection.
     *
     * @return array<int, array<int, string>>
     */
    protected function parseCsv(string $filePath): array
    {
        $rows = [];
        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        // Remove UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^{$bom}/", '', $content);

        // Detect delimiter
        $firstLine = strtok($content, "\r\n");
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;

        foreach ($delimiters as $delim) {
            $count = substr_count((string) $firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        $lines = preg_split("/\r\n|\n|\r/", trim($content));
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $row = str_getcsv($line, $bestDelimiter);
            $rows[] = array_map('trim', $row);
        }

        return $rows;
    }

    /**
     * Parse .xlsx file natively using ZipArchive and SimpleXML without third-party dependencies.
     *
     * @return array<int, array<int, string>>
     */
    protected function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $this->parseCsv($filePath);
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read sheet1.xml
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            return [];
        }

        $xml = simplexml_load_string($sheetXml);
        $zip->close();

        if (! $xml || ! isset($xml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            $colIndex = 0;

            foreach ($row->c as $c) {
                // Determine column index from cell reference e.g. "A1", "B1", "C1"
                $ref = (string) $c['r'];
                $colLetter = preg_replace('/[0-9]/', '', $ref);
                $targetColIndex = $this->columnLetterToIndex($colLetter);

                // Pad missing columns with empty string
                while ($colIndex < $targetColIndex) {
                    $rowData[] = '';
                    $colIndex++;
                }

                $value = isset($c->v) ? (string) $c->v : '';
                $type = isset($c['t']) ? (string) $c['t'] : '';

                if ($type === 's' && isset($sharedStrings[(int) $value])) {
                    $value = $sharedStrings[(int) $value];
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $value = (string) $c->is->t;
                }

                $rowData[] = trim($value);
                $colIndex++;
            }

            if (! empty(array_filter($rowData, fn ($val) => $val !== ''))) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    /**
     * Convert Excel column letter (e.g. 'A', 'B', 'Z', 'AA') to 0-based index.
     */
    protected function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $index = 0;

        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return max(0, $index - 1);
    }

    /**
     * Match column header from possible aliases.
     *
     * @param array<int, string> $headers
     * @param array<int, string> $aliases
     */
    protected function findColumnIndex(array $headers, array $aliases): ?int
    {
        foreach ($headers as $index => $header) {
            foreach ($aliases as $alias) {
                if (str_contains($header, $alias) || $header === $alias) {
                    return $index;
                }
            }
        }

        return null;
    }
}

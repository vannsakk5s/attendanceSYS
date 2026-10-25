<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentImportService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function generateTemplateCsv(): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['student_code', 'full_name', 'gender', 'phone']);
        fputcsv($handle, ['STU-00101', 'Sophea Chan', 'Male', '012345678']);
        fputcsv($handle, ['STU-00102', 'Bopha Kim', 'Female', '098765432']);
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    /**
     * Parse and validate CSV data.
     */
    public function parseAndValidate(UploadedFile $file, int $classroomId): array
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [
                'success' => false,
                'error' => 'Unable to read the uploaded CSV file.',
                'rows' => [],
                'valid_count' => 0,
                'skip_count' => 0,
                'error_count' => 0,
            ];
        }

        // Read header
        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return [
                'success' => false,
                'error' => 'The uploaded file is empty.',
                'rows' => [],
                'valid_count' => 0,
                'skip_count' => 0,
                'error_count' => 0,
            ];
        }

        // Normalize header names
        $headerMap = [];
        foreach ($header as $colIndex => $colName) {
            $cleaned = strtolower(trim($colName));
            $cleaned = preg_replace('/[\x{FEFF}]/u', '', $cleaned); // remove BOM
            $headerMap[$cleaned] = $colIndex;
        }

        if (! isset($headerMap['student_code']) || ! isset($headerMap['full_name'])) {
            fclose($handle);
            return [
                'success' => false,
                'error' => 'Missing required columns in CSV header: "student_code" and "full_name" are required.',
                'rows' => [],
                'valid_count' => 0,
                'skip_count' => 0,
                'error_count' => 0,
            ];
        }

        $codeCol = $headerMap['student_code'];
        $nameCol = $headerMap['full_name'];
        $genderCol = $headerMap['gender'] ?? null;
        $phoneCol = $headerMap['phone'] ?? null;

        // Existing codes in system
        $existingCodes = Student::pluck('student_code')->flip()->all();

        $rows = [];
        $fileSeenCodes = [];
        $rowNumber = 1;
        $validCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (empty(array_filter($data, fn ($val) => trim($val) !== ''))) {
                continue;
            }

            $code = isset($data[$codeCol]) ? trim($data[$codeCol]) : '';
            $name = isset($data[$nameCol]) ? trim($data[$nameCol]) : '';
            $gender = ($genderCol !== null && isset($data[$genderCol])) ? trim($data[$genderCol]) : null;
            $phone = ($phoneCol !== null && isset($data[$phoneCol])) ? trim($data[$phoneCol]) : null;

            if ($gender === '') {
                $gender = null;
            }
            if ($phone === '') {
                $phone = null;
            }

            $rowStatus = 'valid';
            $message = 'Ready to import';

            if ($code === '' || $name === '') {
                $rowStatus = 'error';
                $message = 'Student code and full name cannot be blank.';
                $errorCount++;
            } elseif (isset($fileSeenCodes[$code])) {
                $rowStatus = 'error';
                $message = "Duplicate student code '{$code}' found in file (already at row {$fileSeenCodes[$code]}).";
                $errorCount++;
            } elseif (isset($existingCodes[$code])) {
                $rowStatus = 'skipped_existing';
                $message = "Student code '{$code}' already exists in the system. Skipped by default.";
                $skipCount++;
            } else {
                $validator = Validator::make([
                    'student_code' => $code,
                    'full_name' => $name,
                    'gender' => $gender,
                    'phone' => $phone,
                ], [
                    'student_code' => ['required', 'string', 'max:50'],
                    'full_name' => ['required', 'string', 'max:255'],
                    'gender' => ['nullable', 'string', 'max:20'],
                    'phone' => ['nullable', 'string', 'max:30'],
                ]);

                if ($validator->fails()) {
                    $rowStatus = 'error';
                    $message = implode(' ', $validator->errors()->all());
                    $errorCount++;
                } else {
                    $validCount++;
                }
            }

            if ($code !== '') {
                $fileSeenCodes[$code] = $rowNumber;
            }

            $rows[] = [
                'row_number' => $rowNumber,
                'classroom_id' => $classroomId,
                'student_code' => $code,
                'full_name' => $name,
                'gender' => $gender,
                'phone' => $phone,
                'status' => $rowStatus,
                'message' => $message,
            ];
        }

        fclose($handle);

        return [
            'success' => true,
            'rows' => $rows,
            'valid_count' => $validCount,
            'skip_count' => $skipCount,
            'error_count' => $errorCount,
        ];
    }

    /**
     * Commit valid rows to database inside a transaction.
     */
    public function commitImport(array $validatedRows, int $classroomId, User $teacher): array
    {
        $importedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        DB::transaction(function () use ($validatedRows, $classroomId, $teacher, &$importedCount, &$skippedCount, &$failedCount) {
            foreach ($validatedRows as $row) {
                if ($row['status'] === 'skipped_existing') {
                    $skippedCount++;
                    continue;
                }

                if ($row['status'] !== 'valid') {
                    $failedCount++;
                    continue;
                }

                $exists = Student::where('student_code', $row['student_code'])->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                Student::create([
                    'classroom_id' => $classroomId,
                    'student_code' => $row['student_code'],
                    'full_name' => $row['full_name'],
                    'gender' => $row['gender'] ?? null,
                    'phone' => $row['phone'] ?? null,
                ]);

                $importedCount++;
            }

            $this->auditLogService->log(
                action: 'import_students_csv',
                entity: Student::class,
                entityId: $classroomId,
                oldValues: null,
                newValues: [
                    'classroom_id' => $classroomId,
                    'imported' => $importedCount,
                    'skipped' => $skippedCount,
                    'failed' => $failedCount,
                ],
                actorId: $teacher->id
            );
        });

        return [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
            'failed' => $failedCount,
        ];
    }
}

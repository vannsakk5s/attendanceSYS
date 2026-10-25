<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        $query = Student::whereIn('class_id', $classIds)->with('schoolClass');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('full_name')->paginate(15)->withQueryString();
        $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('class_name')->get();

        return view('students.index', compact('students', 'classes'));
    }

    public function create(Request $request): View
    {
        $teacher = $request->user();
        $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('class_name')->get();
        $selectedClassId = $request->input('class_id');

        return view('students.create', compact('classes', 'selectedClassId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('teacher_id', $teacher->id),
            ],
            'student_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students')->where(fn ($query) => $query->where('class_id', $request->input('class_id'))),
            ],
            'full_name' => ['required', 'string', 'max:150'],
        ]);

        $student = Student::create($validated);

        if ($request->filled('redirect_to_class')) {
            return redirect()->route('classes.show', [$student->class_id, 'tab' => 'students'])
                ->with('success', "Student '{$student->full_name}' ({$student->student_code}) added successfully.");
        }

        return redirect()->route('students.index', ['class_id' => $student->class_id])
            ->with('success', "Student '{$student->full_name}' ({$student->student_code}) added successfully.");
    }

    public function show(Student $student): View
    {
        Gate::authorize('view', $student);

        $student->load([
            'schoolClass',
            'attendanceRecords' => function ($query) {
                $query->latest()->with('attendanceSession');
            }
        ]);

        $totalRecords = $student->attendanceRecords->count();
        $presentRecords = $student->attendanceRecords->where('status', 'present')->count();
        $lateRecords = $student->attendanceRecords->where('status', 'late')->count();
        $absentRecords = $student->attendanceRecords->where('status', 'absent')->count();
        $excusedRecords = $student->attendanceRecords->where('status', 'excused')->count();

        $attendanceRate = $totalRecords > 0
            ? round((($presentRecords + $lateRecords) / $totalRecords) * 100, 1)
            : 0;

        return view('students.show', compact(
            'student',
            'totalRecords',
            'presentRecords',
            'lateRecords',
            'absentRecords',
            'excusedRecords',
            'attendanceRate'
        ));
    }

    public function edit(Student $student, Request $request): View
    {
        Gate::authorize('update', $student);

        $teacher = $request->user();
        $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('class_name')->get();

        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        Gate::authorize('update', $student);

        $teacher = $request->user();

        $validated = $request->validate([
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('teacher_id', $teacher->id),
            ],
            'student_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students')
                    ->where(fn ($query) => $query->where('class_id', $request->input('class_id')))
                    ->ignore($student->id),
            ],
            'full_name' => ['required', 'string', 'max:150'],
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student details updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        Gate::authorize('delete', $student);

        $classId = $student->class_id;
        $name = $student->full_name;
        $student->delete();

        return redirect()->route('classes.show', [$classId, 'tab' => 'students'])
            ->with('success', "Student '{$name}' has been removed.");
    }

    public function importCsvForm(Request $request): View
    {
        $teacher = $request->user();
        $classes = SchoolClass::where('teacher_id', $teacher->id)->orderBy('class_name')->get();
        $selectedClassId = $request->input('class_id');

        return view('students.import', compact('classes', 'selectedClassId'));
    }

    public function downloadExcelTemplate()
    {
        $path = public_path('templates/students_import_template.xlsx');
        if (!file_exists($path)) {
            require_once base_path('scripts/generate_excel_template.php');
        }

        return response()->download($path, 'students_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadCsvTemplate(): Response
    {
        $csvContent = "student_code,full_name\nSTU-00101,Sophea Chan\nSTU-00102,Bopha Kim\nSTU-00103,Rathana Seng\n";

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
        ]);
    }

    public function processCsvImport(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $request->validate([
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('teacher_id', $teacher->id),
            ],
            'file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
            'csv_file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ]);

        $file = $request->file('file') ?? $request->file('csv_file');
        if (!$file) {
            return back()->withErrors(['file' => 'Please select an Excel (.xlsx) or CSV file to import.']);
        }

        $class = SchoolClass::findOrFail($request->input('class_id'));
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = [];
        if (in_array($extension, ['xlsx', 'xls'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            // Remove header row
            if (!empty($sheetData)) {
                array_shift($sheetData);
            }
            $rows = $sheetData;
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            fgetcsv($handle); // skip header
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (count($row) < 2 || empty(trim($row[0] ?? '')) || empty(trim($row[1] ?? ''))) {
                continue;
            }

            $code = trim((string) $row[0]);
            $name = trim((string) $row[1]);

            // Unique check for class
            $exists = Student::where('class_id', $class->id)->where('student_code', $code)->exists();
            if ($exists) {
                $skipped++;
                continue;
            }

            Student::create([
                'class_id' => $class->id,
                'student_code' => $code,
                'full_name' => $name,
            ]);
            $imported++;
        }

        return redirect()->route('classes.show', [$class, 'tab' => 'students'])
            ->with('success', "Import completed: {$imported} students added" . ($skipped > 0 ? ", {$skipped} skipped (duplicate code)." : "."));
    }
}

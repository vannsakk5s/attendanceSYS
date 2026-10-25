<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentCsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected Teacher $teacher;
    protected SchoolClass $class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacher = Teacher::factory()->create();
        $this->class = SchoolClass::factory()->create(['teacher_id' => $this->teacher->id]);
    }

    public function test_csv_template_download_returns_valid_csv(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('students.import.template'));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="students_import_template.csv"');
        $this->assertStringContainsString('student_code,full_name', $response->getContent());
    }

    public function test_excel_template_download_returns_xlsx_file(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('students.import.excel-template'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
    }

    public function test_teacher_can_import_students_via_csv(): void
    {
        $content = "student_code,full_name\nSTU-1001,John Doe\nSTU-1002,Jane Smith\n";
        $file = UploadedFile::fake()->createWithContent('students.csv', $content);

        $response = $this->actingAs($this->teacher)->post(route('students.import.process'), [
            'class_id' => $this->class->id,
            'file' => $file,
        ]);

        $response->assertRedirect(route('classes.show', [$this->class, 'tab' => 'students']));
        $this->assertDatabaseHas('students', [
            'class_id' => $this->class->id,
            'student_code' => 'STU-1001',
            'full_name' => 'John Doe',
        ]);
        $this->assertDatabaseHas('students', [
            'class_id' => $this->class->id,
            'student_code' => 'STU-1002',
            'full_name' => 'Jane Smith',
        ]);
    }

    public function test_teacher_can_import_students_via_excel_file(): void
    {
        $templatePath = public_path('templates/students_import_template.xlsx');
        if (!file_exists($templatePath)) {
            require base_path('scripts/generate_excel_template.php');
        }

        $file = new UploadedFile(
            $templatePath,
            'students.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->teacher)->post(route('students.import.process'), [
            'class_id' => $this->class->id,
            'file' => $file,
        ]);

        $response->assertRedirect(route('classes.show', [$this->class, 'tab' => 'students']));
        $this->assertDatabaseHas('students', [
            'class_id' => $this->class->id,
            'student_code' => 'STU-00101',
            'full_name' => 'Sophea Chan',
        ]);
    }

    public function test_duplicate_student_codes_are_skipped(): void
    {
        Student::create([
            'class_id' => $this->class->id,
            'student_code' => 'STU-1001',
            'full_name' => 'Existing Student',
        ]);

        $content = "student_code,full_name\nSTU-1001,Existing Duplicate\nSTU-1003,New Student\n";
        $file = UploadedFile::fake()->createWithContent('students.csv', $content);

        $response = $this->actingAs($this->teacher)->post(route('students.import.process'), [
            'class_id' => $this->class->id,
            'file' => $file,
        ]);

        $response->assertRedirect(route('classes.show', [$this->class, 'tab' => 'students']));
        $this->assertDatabaseHas('students', [
            'class_id' => $this->class->id,
            'student_code' => 'STU-1003',
            'full_name' => 'New Student',
        ]);
        $this->assertEquals(2, Student::where('class_id', $this->class->id)->count());
    }
}

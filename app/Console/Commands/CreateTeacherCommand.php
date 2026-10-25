<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateTeacherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:teacher
                            {--name= : The teacher name}
                            {--email= : The teacher email}
                            {--password= : The teacher password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely and interactively create a new Teacher account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('--- Create New Teacher Account ---');

        $name = $this->option('name') ?: $this->ask('Enter teacher full name');
        $email = $this->option('email') ?: $this->ask('Enter teacher email address');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:teachers,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $password = $this->option('password');
        if (! $password) {
            $password = $this->secret('Enter teacher password');
            $confirmPassword = $this->secret('Confirm teacher password');

            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match.');
                return self::FAILURE;
            }
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return self::FAILURE;
        }

        $teacher = Teacher::create([
            'name' => $name,
            'email' => $email,
            'password_hash' => Hash::make($password),
        ]);

        $this->info("Teacher account [{$teacher->email}] created successfully!");

        return self::SUCCESS;
    }
}

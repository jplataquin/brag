<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a new admin user interactively';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating a new Admin user...');

        $firstname = $this->ask('First Name', 'System');
        $lastname = $this->ask('Last Name', 'Admin');
        $username = $this->ask('Username');
        $email = $this->ask('Email Address');
        $birthdate = $this->ask('Birthdate (YYYY-MM-DD)', '1990-01-01');
        $password = $this->secret('Password');
        $confirmPassword = $this->secret('Confirm Password');

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match!');
            return 1;
        }

        $validator = Validator::make([
            'username' => $username,
            'email' => $email,
            'birthdate' => $birthdate,
            'password' => $password,
        ], [
            'username' => ['required', 'string', 'max:30', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'birthdate' => ['required', 'date', 'date_format:Y-m-d'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $user = User::create([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'birthdate' => $birthdate,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Admin user '{$user->username}' created successfully!");
        
        return 0;
    }
}

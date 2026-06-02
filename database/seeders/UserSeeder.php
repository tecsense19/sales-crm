<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'phone' => '+1 234 567 8900',
            'bio' => 'System Administrator with full access.',
            'country' => 'USA',
            'location' => 'New York',
            'postal_code' => '10001',
            'tax_id' => 'TX-12345',
            'facebook' => 'https://facebook.com/admin',
            'twitter' => 'https://x.com/admin',
            'linkedin' => 'https://linkedin.com/in/admin',
            'instagram' => 'https://instagram.com/admin',
        ]);

        // 2 Employees
        User::create([
            'name' => 'Employee One',
            'email' => 'employee1@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'employee',
            'phone' => '+1 456 789 0123',
            'bio' => 'Senior Sales Representative.',
            'country' => 'USA',
            'location' => 'Austin',
            'postal_code' => '73301',
            'tax_id' => 'TX-11111',
            'facebook' => 'https://facebook.com/employee1',
            'twitter' => 'https://x.com/employee1',
            'linkedin' => 'https://linkedin.com/in/employee1',
            'instagram' => 'https://instagram.com/employee1',
        ]);

        User::create([
            'name' => 'Employee Two',
            'email' => 'employee2@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'employee',
            'phone' => '+1 567 890 1234',
            'bio' => 'Junior Sales Representative.',
            'country' => 'USA',
            'location' => 'Seattle',
            'postal_code' => '98101',
            'tax_id' => 'TX-22222',
            'facebook' => 'https://facebook.com/employee2',
            'twitter' => 'https://x.com/employee2',
            'linkedin' => 'https://linkedin.com/in/employee2',
            'instagram' => 'https://instagram.com/employee2',
        ]);

        User::create([
            'name' => 'Employee Three',
            'email' => 'employee3@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'employee',
            'phone' => '+61 2 1234 5678',
            'bio' => 'Customer Support Specialist.',
            'country' => 'Australia',
            'location' => 'Sydney',
            'postal_code' => '2000',
            'tax_id' => 'AU-33333',
            'facebook' => 'https://facebook.com/employee3',
            'twitter' => 'https://x.com/employee3',
            'linkedin' => 'https://linkedin.com/in/employee3',
            'instagram' => 'https://instagram.com/employee3',
        ]);
    }
}

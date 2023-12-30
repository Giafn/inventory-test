<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'role' => 1,
            ],
            [
                'name' => 'Sales',
                'username' => 'sales',
                'password' => bcrypt('sales'),
                'role' => 2,
            ],
            [
                'name' => 'Purchase',
                'username' => 'purchase',
                'password' => bcrypt('purchase'),
                'role' => 3,
            ],
            [
                'name' => 'Manager',
                'username' => 'manager',
                'password' => bcrypt('manager'),
                'role' => 4,
            ],
        ];

        User::insert($list);
    }
}

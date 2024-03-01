<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config("constants.role");
        foreach ($roles as $key => $value) {
            DB::table('roles')->insert([
                "name" => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

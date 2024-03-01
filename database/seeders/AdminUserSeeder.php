<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = config("constants.role.user_admin");
        $admin_role_id = DB::table("roles")->where('name', $admin_role) -> value("id");
                // Create an admin user
        DB::table('users')->insert([
            'name' => 'Universe Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'), 
            'phone'=> '0353537180',// Hash the password
            'street_address'=> '99 Tô Hiến Thành, Lê Hữu Trác',
            'district_address'=> 'Sơn Trà',
            'city'=> 'Đà Nẵng',
            'avatar'=> 'https://i.pinimg.com/564x/24/21/85/242185eaef43192fc3f9646932fe3b46.jpg',
            'role_id' => $admin_role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

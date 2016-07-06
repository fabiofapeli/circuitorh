<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");

        User::truncate();

        User::create(['name'=>'Administrador','email'=>'admin','password'=>bcrypt('123456')]);
    }
}

<?php

use Illuminate\Database\Seeder;

use App\Models\Users\Admin;

class SampleAdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Admin::class, 12)->create();
    }
}

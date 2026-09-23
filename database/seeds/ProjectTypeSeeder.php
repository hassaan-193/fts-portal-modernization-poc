<?php

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('project_types')->truncate();

        // create types
        ProjectType::create(['name' => 'Installation']);
        ProjectType::create(['name' => 'Supply']);
        ProjectType::create(['name' => 'Maintenance']);
        ProjectType::create(['name' => 'CCTV']);
        ProjectType::create(['name' => 'Trading']);
        ProjectType::create(['name' => 'It']);
    }

}

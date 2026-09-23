<?php

use App\Models\LpoOutType;
use Illuminate\Database\Seeder;

class LpoOutTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('lpo_out_types')->truncate();

        // create types
        LpoOutType::create(['name' => 'Genral']);
        LpoOutType::create(['name' => 'Project']);
    }

}

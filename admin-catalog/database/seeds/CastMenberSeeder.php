<?php

use App\Models\CastMenber;
use Illuminate\Database\Seeder;

class CastMenberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CastMenber::class, 100)->create();
    }
}

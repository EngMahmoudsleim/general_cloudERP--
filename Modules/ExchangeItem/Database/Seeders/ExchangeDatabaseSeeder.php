<?php

namespace Modules\ExchangeItem\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ExchangeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Uncomment to run demo seeder
        $this->call(ExchangeDemoSeeder::class);
    }
}

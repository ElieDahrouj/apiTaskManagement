<?php

namespace Database\Seeders;

use App\Models\Tasks;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $task = new Tasks();
            $task->body = $faker->realText(100);
            $task->user_id = mt_rand(1,2);
            $task->completed = false;
            $task->save();
        }
    }
}

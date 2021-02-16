<?php

namespace Tests\Feature;

use App\Models\Tasks;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Database\Factories\TaskFactory;
class TaskTest extends TestCase
{
    /**
     * @test
     */
    public function showAllTask()
    {
        Sanctum::actingAs(
            User::find(1),['*']
        );
        $this->get('/api/task')->assertStatus(200);
    }

    /**
     * @test
     */
    public function showAllTaskCompleted()
    {
        Sanctum::actingAs(
            User::find(1),['*']
        );
        $this->get('/api/task?completed=true')->assertStatus(200);
    }

    /**
     * @test
     */
    public function showAllTasksUserNotConnected(){
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->get('/api/task')->assertStatus(401);
    }

    /**
     * @test
     */
    public function showAllTaskCompletedUserNotConnected()
    {
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->get('/api/task?completed=true')->assertStatus(401);
    }

    /**
     * @test
     */
    public function createTaskSucceed()
    {
        Sanctum::actingAs(
            User::find(1),['*']
        );
        $this->post('/api/task',['body' =>Faker::create('fr_FR')->realText(100),'user_id'=>User::find(1)['id'],'completed'=>false])->assertStatus(200);
    }

    /**
     * @test
     */
    public function missingBodyToCreateTask(){

        Sanctum::actingAs(
            User::find(1),['*']
        );
        $this->post('/api/task',['user_id'=>User::find(1)['id'],'completed'=>false])->assertStatus(422);
    }

    /**
     * @test
     */
    public function createTaskWithoutUserConnected()
    {
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->post('/api/task',['body' =>Faker::create('fr_FR')->sentence(30)])
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function createTaskWithoutUserConnectedAndParameter(){
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->post('/api/task',[])
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function showOneTaskForUserConnected()
    {
        $user = User::factory(User::class)->create();
        $task = Tasks::factory(Tasks::class)->create([
            'user_id' => $user->getAttribute('id')
        ]);
        $this->actingAs($user)->get('/api/task/'.$task->getAttribute('id'))->assertStatus(200);
    }

    /**
     * @test
     */
    public function showOneTaskForUserNotConnected()
    {
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->get('/api/task/10')->assertStatus(401);
    }

    /**
     * @test
     */
    public function showOneTaskForAnotherUser()
    {
        Sanctum::actingAs(
            User::find(1),['*']
        );
        $user = User::factory(User::class)->create();
        $task = Tasks::factory(Tasks::class)->create();

        $this->actingAs($user)->get('/api/task/'.$task->getAttribute('id'))->assertStatus(422);
    }

    /**
     * @test
     */
    public function showOneTaskNotExistForUserConnected()
    {
        Sanctum::actingAs(
            User::find(1),['*']
        );
        $this->get('/api/task/1855')->assertStatus(422);
    }

    /**
     * @test
     */
    public function deleteOneTaskForUserConnected(){

        $user = User::factory(User::class)->create();
        $task = Tasks::factory(Tasks::class)->create([
            'user_id' => $user->getAttribute('id')
        ]);
        $this->actingAs($user)->delete('/api/task/'.$task->getAttribute('id'))->assertStatus(200);
    }

    /**
     * @test
     */
    public function deleteOneTaskNotYoursForUserConnected(){
        Sanctum::actingAs(
            User::factory(User::class)->make()
        );
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->delete('/api/task/1')->assertStatus(422);
    }

    /**
     * @test
     */
    public function deleteOneTaskForUserNotConnected(){
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->delete('/api/task/8')->assertStatus(401);
    }

    /**
     * @test
     */
    public function updateOneTaskForUserNotConnected(){
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->put('/api/task/8')->assertStatus(401);
    }

    /**
     * @test
     */
    public function updateOneTaskNotYoursForUserConnected(){
        Sanctum::actingAs(
            User::factory(User::class)->make()
        );
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->put('/api/task/1')->assertStatus(401);
    }


    /**
     * @test
     */
    public function updateOneTaskSucceed(){
        $user = User::factory(User::class)->create();
        $task = Tasks::factory(Tasks::class)->create([
            'user_id' => $user->getAttribute('id')
        ]);
        $this->withHeaders([
            'Accept'=>'Application/json',
        ])->actingAs($user)->put('/api/task/'.$task->getAttribute('id'),['completed'=>true])->assertStatus(200);
    }
}

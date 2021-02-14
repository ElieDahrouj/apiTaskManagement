<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Faker\Factory as Faker;
class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function registerSucceed(){
        $this->post('/api/register',['name' =>Faker::create('fr_FR')->name,'email'=>Faker::create('fr_FR')->email,'password'=>'secret'])->assertStatus(200);
    }

    /**
     * @test
     */
    public function registerForgotName(){
        $this->post('/api/register',['email'=>Faker::create('fr_FR')->email,'password'=>Hash::make('password')])->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerForgotEmail(){
        $response  = $this->post('/api/register',['name' =>'jordan','password'=>Hash::make('password')]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerForgotPassword(){
        $response  = $this->post('/api/register',['name' =>'jordan','email'=>'jordan@hotmail.com']);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerForgotPasswordAndEmail(){
        $this->post('/api/register',['name' =>'jordan'])->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerForgotPasswordAndName(){
        $faker = Faker::create('fr_FR');
        $this->post('/api/register',['email' =>$faker->email])->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerForgotEmailAndName(){
        $this->post('/api/register',['password' =>'secret'])->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerForgotAllParameter(){
        $response  = $this->post('/api/register',[]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function registerFormatNotValidForEmail(){
        $response  = $this->post('/api/register',['name' =>'jordan','email'=>'jordan.mickael','password'=>Hash::make('password')]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */

    public function loginSucceed(){
        $getOneUserTest = User::first();
        $response  = $this->post('/api/login',['email'=>$getOneUserTest['email'],'password'=>'secret']);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function loginForgotEmail(){
        $response  = $this->post('/api/login',['password'=>Hash::make('password')]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function loginForgotPassword(){
        $this->post('/api/login',['email'=>'jordan@hotmail.com'])->assertStatus(422);
    }

    /**
     * @test
     */
    public function loginForgotAllParameter(){
        $this->post('/api/login',[])->assertStatus(422);
    }

    /**
     * @test
     */
    public function loginWrongPassword(){
        $this->post('/api/login',['email'=>User::first()['email'],'password'=>'wrongpassword'])->assertStatus(401);
    }

    /**
     * @test
     */
    public function loginWrongEmail(){
        $this->post('/api/login',['email'=>"kdkd@gmail.com",'password'=>'secret'])->assertStatus(401);
    }

    /**
     * @test
     */
    public function loginWrongPasswordAndEmail(){
        $this->post('/api/login',['email'=>'azert@gmail.com','password'=>'wrongpassword'])->assertStatus(401);
    }
}

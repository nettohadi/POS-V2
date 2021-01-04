<?php

namespace Tests\Feature\Controllers;

use App\Exceptions\ApiAuthorizationException;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public $tableName = 'users';
    public $routeName = 'users';
    public $paramName = 'user';
    private $fakeImageName = 'user.jpg';

   /** @test **/
   public function users_can_be_retrieved()
   {
       $this->withoutExceptionHandling();

       /* 1. Setup --------------------------------*/
       $this->withAuthorization();
       User::factory()->count(10)->create();
       $users = User::take(15)->get()->toArray();

           /* 2. Invoke --------------------------------*/
       $response = $this->get(route('users.index'));

       /* 3. Assert --------------------------------*/
       $response->assertStatus(200);
       $this->assertEquals($users, $response->json('data'));
   }

   /** @test **/
   public function users_can_be_retrieved_with_pagination()
   {
       /* 1. Setup --------------------------------*/
       $this->withAuthorization();
       User::factory()->count(10)->create();
       $users = User::take(5)->get()->toArray();

       /* 2. Invoke --------------------------------*/
       $response = $this->get(route('users.index') . '?perPage=5');

       /* 3. Assert --------------------------------*/
       $response->assertStatus(200);
       $this->assertEquals($users, $response->json('data'));
   }

   /** @test **/
   public function users_can_be_filtered_by_name()
   {
       /* 1. Setup --------------------------------*/
       $this->withAuthorization();

       //create 2 users with names contain 'abdul'
       $expectedUsers = [];
       $expectedUsers[] = User::factory()->create(['name' => 'Abdul Hadi'])->toArray();
       $expectedUsers[] = User::factory()->create(['name' => 'Abdul Muid'])->toArray();

       //create 5 random users
       User::factory()->count(5)->create();

       /* 2. Invoke --------------------------------*/
       $response = $this->get(route('users.index').'?name=abdul');

       /* 3. Assert --------------------------------*/
       $response->assertStatus(200);
       $this->assertEquals($expectedUsers, $response->json('data'));
   }

    /** @test **/
    public function users_will_be_empty_if_names_does_not_match_the_keywords()
    {
        /* 1. Setup ------------------------------ */
        $this->withAuthorization();

        //Create two users which has the word 'abdul'

        User::factory()->create([
            'name' => 'Abdul Hadi'
        ]);

        User::factory()->create([
            'name' => 'Abdul Muid'
        ]);

        //Create 10 random users
        User::factory()->count(5)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('users.index').'?name=random_name');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(0,$response->json('data'));
        $this->assertEquals([], $response->json('data'));
    }

    /** @test **/
    public function a_user_can_be_found()
    {
        $this->withoutExceptionHandling();

        /*Setup ---------------------------------*/
        $this->withAuthorization();
        $user = User::factory()->create();

        /*Invoke ---------------------------------*/
        $response = $this->get(route('users.show',['user' => $user->id]));

        /*Assert ---------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($user->toArray(),$response->json('data'));

    }

    /** @test **/
    public function a_user_can_not_be_found()
    {
        $this->expectNotFoundException();

        /* Setup */
        $this->withAuthorization();
        $user = User::factory()->create();

        /* Invoke */
        $response = $this->get(route('users.show',['user' => 'random_id']));

        /* Assert */
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    /**
     * @test *
     * @dataProvider validUser
     *
     */
    public function a_user_can_be_created($field, $value)
    {
        /* 1. Setup ------------------------------ */
        $this->withAuthorization();
        $user = $this->makeUser();
        $user[$field] = $value;
        /* 2. Invoke ------------------------------ */
        $response = $this->post(route('users.store'),$user);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(201);


        $data = $response->json('data');

        $this->removeColumn($data,['created_at','updated_at','id']);
        $this->removeColumn($user, ['image','password','password_confirm']);
        $this->assertEquals($user, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName, $user);
    }

    /**
     * @test *
     * @dataProvider invalidInsertUser
     *
     */
    public function a_user_can_not_be_created($field, $value)
    {
        /* 1. Setup ------------------------------ */
        $this->withAuthorization();
        $user = $this->makeUser();
        $user[$field] = $value;

        /* 2. Invoke ------------------------------ */
        $response = $this->post(route('users.store'),$user);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);

        /* 3.2 Database --------------------------------*/
        $this->removeColumn($user, ['image','password','password_confirm']);
        $this->assertDatabaseMissing($this->tableName, $user);
    }

    /** @test **/
    public function a_user_image_can_be_uploaded_during_creation()
    {
        $this->withoutExceptionHandling();
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        //create a fake storage
        Storage::fake('public');
        $data = $this->makeUser();
        //add fake image file
        $data['image'] = UploadedFile::fake()->image($this->fakeImageName);

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('users.store'),$data);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(201);
        $savedUser = $response->json('data');

        // Assert the file was stored
        Storage::disk('public')->assertExists($savedUser['image']);
    }

    /**
     * @test *
     * @dataProvider validUser
     */
    public function a_user_can_be_updated($field, $value)
    {
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        $user = $this->getUserFromDB();
        //edit data
        $user[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('users.update',['user' => $user['id']]),$user);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(200);

        $data = $response->json('data');

        //remove image & timestamp because we can not compare them-------
        $this->removeTimeStampAndImage($data);
        $this->removeTimeStampAndImage($user);
        //---------------------------------------------------

        $this->assertEquals($user, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName,$data);
    }

    /**
     * @test *
     * @dataProvider invalidUser
     */
    public function a_user_can_not_be_updated($field, $value)
    {
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        $user = $this->getUserFromDB();
        //edit data
        $user[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('users.update',['user' => $user['id']]),$user);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName,$user);
    }

    /** @test **/
    public function a_user_image_can_be_uploaded_during_update()
    {
        $this->withoutExceptionHandling();
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        //create a fake storage
        Storage::fake('public');
        $user = $this->makeUser();
        //add fake image file
        $user['image'] = UploadedFile::fake()->image($this->fakeImageName);

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('users.store'),$user);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(201);
        $data = $response->json('data');

        // assert database
        $this->removeTimeStamp($data);
        $this->assertDatabaseHas($this->tableName, $data);

        // Assert the file was stored
        Storage::disk('public')->assertExists($data['image']);
    }

    /** @test **/
    public function a_user_can_be_deleted()
    {
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        $user = User::factory()->create()->toArray();
        $this->removeTimeStamp($user);

        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$user);

        /* 2. Invoke --------------------------------*/
        $response = $this->delete(route('users.destroy',['user' => $user['id']]));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(200);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $user);
    }

    /** @test */
    public function a_user_can_not_be_deleted_if_does_not_exist()
    {
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        $user = User::factory()->create()->toArray();
        $this->removeTimeStamp($user);

        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$user);

        /* 2. Invoke --------------------------------*/
        // we pass  id of user we did not create
        $response = $this->delete(route('users.destroy',['user' => 'no_id']));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(404);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, ['id' => 'no_id']);
        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$user);
    }

    /**
     * @test
     * @dataProvider routes
     * @param $method*
     * @param $routeName
     * @param $param
     */
    public function user_can_not_be_accessed_if_unauthenticated($method, $routeName, $param)
    {
        /* 1. Setup --------------------------------*/

        /* 2. Invoke --------------------------------*/
        $response = $this->call($method,route($routeName,$param));

        /* 3. Assert --------------------------------*/
        $response->assertStatus(401);

    }

    /**
     * @test
     * @dataProvider routes
     */
    public function user_can_not_be_accessed_if_unauthorized($method, $routeName, $param)
    {
        /* 1. Setup --------------------------------*/
        $this->withoutExceptionHandling();
        $this->expectException(ApiAuthorizationException::class);

        //Exclude particular route
        $this->withAuthorizationExcept([$routeName]);

        /* 2. Invoke --------------------------------*/
        $response = $this->call($method,route($routeName,$param));

        /* 3. Assert --------------------------------*/
        $response->assertStatus(403);

    }

    public function routes(){
        return $this->getRoutes();
    }

    private function getUserFromDB(array $values=[]){
        $outlet = Outlet::factory()->create();
        $values['outlet_id'] = $outlet->id;

        //create user in DB & return it
        return User::factory()->create($values)->toArray();
    }
    private function makeUser()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->make(['outlet_id' => $outlet->id])->toArray();
        $user['password'] = Hash::make('12345678');
        $user['password_confirm'] = Hash::make('12345678');
        return $user;
    }
    public function validUser()
    {
        return [
            'Image is not required' => ['image',null],
            'Shift Id not required' => ['shift_id',null],
        ];
    }
    public function invalidUser()
    {
        return [
            'Name is required'                        => ['name',null],
            'Sex  is required'                        => ['sex',null],
            'Email is required'                       => ['email',null],
            'Email must be valid'                     => ['email','email'],
            'role is required'                        => ['role',null],
            'Image must be image file'                => ['image','not-image']
        ];
    }
    public function invalidInsertUser(){
        return array_merge($this->invalidUser(), [
            'Password is required'                    => ['password',null],
            'Password Confirmation is required'       => ['password_confirm',null],
            'Password must be at least 6 characters'  => ['password','123'],
        ]);
    }
}

<?php

namespace Touhidurabir\ModelHashid\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Touhidurabir\ModelHashid\Tests\App\User;
use Touhidurabir\ModelHashid\Tests\App\Profile;
use Touhidurabir\ModelHashid\Tests\Traits\LaravelTestBootstrapping;

class ModelTraitTest extends TestCase {

    use LaravelTestBootstrapping;

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations() {

        $this->loadMigrationsFrom(__DIR__ . '/App/database/migrations');
        
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }


    /**
     * @test
     */
    public function it_add_an_hashid_on_creation_to_model_record() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);

        $this->assertIsString($user->hash_id);
    }


    /**
     * @test
     */
    public function the_decoded_hashed_id_is_same_as_model_id() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);

        $this->assertEquals(decode_hashid($user->hash_id), $user->id);
    }


    /**
     * @test
     */
    public function it_can_hash_based_on_model_specific_column_name() {

        $profile = Profile::create(['first_name' => 'first', 'last_name' => 'last']);

        $this->assertIsString($profile->{$profile->getHashColumn()});
        $this->assertIsInt(decode_hashid($profile->{$profile->getHashColumn()}));
        $this->assertEquals(decode_hashid($profile->{$profile->getHashColumn()}), $profile->id);
    }


    /**
     * @test
     */
    public function the_getHashId_method_can_return_the_hash_id() {

        $profile = Profile::create(['first_name' => 'first', 'last_name' => 'last']);

        $this->assertIsString($profile->getHashId());
    }   
    

    /**
     * @test
     */
    public function the_getHashIdFieldName_method_return_the_proper_hash_column_name() {

        $profile = new Profile;
        $this->assertEquals($profile->getHashIdFieldName(), 'hash');

        $user = new User;
        $this->assertEquals($user->getHashIdFieldName(), 'hash_id');
    }


    /**
     * @test
     */
    public function the_getOriginalIdFromHashId_return_original_id_by_deahshing_hashid() {

        $profile = Profile::create(['first_name' => 'first', 'last_name' => 'last']);
        $this->assertEquals($profile->getOriginalIdFromHashId(), $profile->id);

        $profile->{$profile->getHashIdFieldName()} = null;
        $profile->save();
        $this->assertEquals($profile->getOriginalIdFromHashId(), $profile->id);
    }


    /**
     * @test
     */
    public function the_findByHashId_can_find_a_model_record() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $hash = $user->getHashId();
        
        $this->assertEquals(User::findByHashId($hash)->id, $user->id);

        $user1 = User::create(['email' => 'mail1@m.test', 'password' => '123']);
        $user2 = User::create(['email' => 'mail2@m.test', 'password' => '123']);

        $this->assertEquals(User::findByHashId([$user1->hash_id, $user2->hash_id])->count(), 2);
        $this->assertTrue(User::findByHashId([$user1->hash_id, $user2->hash_id]) instanceof Collection);
    }


    /**
     * @test
     */
    public function it_can_query_model_using_byHashId_local_scope() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);

        $hash = $user->getHashId();

        $searchedUser = User::byHashId($hash)->first();

        $this->assertEquals($searchedUser->id, $user->id);
        $this->assertNull(User::byHashId('sifdsfldsfsdfsd')->first());


        $user1 = User::create(['email' => 'mail10@m.test', 'password' => '123']);
        $user2 = User::create(['email' => 'mail20@m.test', 'password' => '123']);

        $this->assertEquals(User::byHashId([$user1->hash_id, $user2->hash_id])->get()->count(), 2);
        $this->assertTrue(User::byHashId([$user1->hash_id, $user2->hash_id])->get() instanceof Collection);
    }

}
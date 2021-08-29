<?php

namespace Touhidurabir\ModelHashid\Tests;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Route;
use Touhidurabir\ModelHashid\Tests\App\User;
use Touhidurabir\ModelHashid\Tests\App\Profile;
use Touhidurabir\ModelHashid\Http\Middleware\DehashRouteParams;
use Touhidurabir\ModelHashid\Http\Middleware\DehashRequestParams;
use Touhidurabir\ModelHashid\Tests\Traits\LaravelTestBootstrapping;

class MiddlewareTest extends TestCase {

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
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router) {
        
        Route::get('/users/{user}', function($user) {
            
            return $user;

        })->middleware(['web', DehashRouteParams::class]);


        Route::get('/users/{user}/profiles/{profile}', function($user, $profile) {
            
            return [$user, $profile];

        })->middleware(['web', DehashRouteParams::class]);


        Route::post('/api/users/{user}', function(Request $request, $user) {
            
            return response()->json([
                'user_id' => $user,
                'profile_id' => $request->get('profile_id'),
            ]);

        })->middleware(['web', DehashRouteParams::class, DehashRequestParams::class]);


        Route::get('/profiles/{profile}', function(Request $request, $profile) {
            
            return response()->json([
                'user_id' => $request->get('user_id'),
                'profile_id' => $profile,
            ]);

        })->middleware(['web', DehashRouteParams::class, DehashRequestParams::class]);
    }


    /**
     * @test
     */
    public function the_DehashRouteParams_middleware_can_dehash_route_params() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = Profile::create(['first_name' => 'first', 'last_name' => 'last']);
        
        $this->get("/users/{$user->hash_id}")->assertOk();
        $this->get("/users/{$user->hash_id}/profiles/{$profile->hash}")->assertOk();

        $this->get("/users/{$user->hash_id}")->assertSeeText($user->id);
        $this->get("/users/{$user->hash_id}/profiles/{$profile->hash}")->assertSeeInOrder([$user->id, $profile->id]);

        $this->get("/users/{$user->hash_id}")->assertDontSee($user->getHashId());
        $this->get("/users/{$user->hash_id}/profiles/{$profile->hash}")->assertDontSee([$user->getHashId(), $profile->getHashId()]);
    }


    /**
     * @test
     */
    public function the_DehashRequestParams_middleware_can_dehash_post_request_params() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = Profile::create(['first_name' => 'first', 'last_name' => 'last']);
        
        $response = $this->postJson("/api/users/{$user->hash_id}", ['profile_id' => $profile->getHashId()]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'profile_id' => $profile->id
            ]);
    }


    /**
     * @test
     */
    public function the_DehashRequestParams_middleware_can_dehash_get_request_params() {

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = Profile::create(['first_name' => 'first', 'last_name' => 'last']);

        $response = $this->get("/profiles/{$profile->hash}?user_id={$user->hash_id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'profile_id' => $profile->id
            ]);
    }
    
}

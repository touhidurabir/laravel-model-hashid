<?php

namespace Touhidurabir\ModelHashid\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Touhidurabir\ModelHashid\Tests\App\User;
use Touhidurabir\ModelHashid\Tests\App\Profile;
use Touhidurabir\ModelHashid\Jobs\ModelHashidRegeneratorJob;
use Touhidurabir\ModelHashid\Tests\Traits\LaravelTestBootstrapping;

class RegeneratingJobTest extends TestCase {

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
    public function the_job_will_run() {

        Bus::fake();

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);
        $profile = Profile::create(['first_name' => 'First_Name', 'last_name' => 'Last_Name']);

        ModelHashidRegeneratorJob::dispatch(
            [Touhidurabir\ModelHashid\Tests\App\User::class, Touhidurabir\ModelHashid\Tests\App\Profile::class],
            true
        );

        Bus::assertDispatched(ModelHashidRegeneratorJob::class, function ($job) use ($user, $profile) {
            return true;
        });
    }


    /**
     * @test
     */
    public function the_job_can_fill_out_missing_hash_id() {

        User::disbaleHashIdGeneration();

        $user = User::create(['email' => 'mail@m.test', 'password' => '123']);

        $this->assertNull($user->getHashId());

        ModelHashidRegeneratorJob::dispatchNow(
            [\Touhidurabir\ModelHashid\Tests\App\User::class],
            true
        );

        $user->refresh();

        $this->assertNotNull($user->getHashId());
    }

}

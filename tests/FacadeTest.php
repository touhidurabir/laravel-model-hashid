<?php

namespace Touhidurabir\ModelHashid\Tests;

use PHPUnit\Framework\Attributes\Test;
use Orchestra\Testbench\TestCase;
use Touhidurabir\ModelHashid\Facades\ModelHashid;
use Touhidurabir\ModelHashid\Tests\Traits\LaravelTestBootstrapping;

class FacadeTest extends TestCase
{
    use LaravelTestBootstrapping;

    /**
     * @test
     */
    #[Test]
    public function the_facade_can_encode_and_decode()
    {

        $this->assertSame(1, ModelHashid::decode(ModelHashid::encode(1)));
    }


    /**
     * @test
     */
    #[Test]
    public function the_package_registers_only_the_model_hashid_alias()
    {

        $composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);

        $aliases = $composer['extra']['laravel']['aliases'] ?? [];

        $this->assertSame(ModelHashid::class, $aliases['ModelHashid'] ?? null);

        // the copy-pasted ModelUuid alias collided with laravel-model-uuid's facade
        $this->assertArrayNotHasKey('ModelUuid', $aliases);
    }
}

<?php

namespace Touhidurabir\ModelHashid\Tests;

use PHPUnit\Framework\Attributes\Test;
use Orchestra\Testbench\TestCase;
use Touhidurabir\ModelHashid\Hasher\Hasher;
use Touhidurabir\ModelHashid\Tests\Traits\LaravelTestBootstrapping;

class HelperTest extends TestCase
{
    use LaravelTestBootstrapping;

    /**
     * @test
     */
    #[Test]
    public function the_decode_hashids_method_is_callable()
    {

        $this->assertTrue(is_callable('decode_hashids'));
    }


    /**
     * @test
     */
    #[Test]
    public function the_decode_hashid_method_is_callable()
    {

        $this->assertTrue(is_callable('decode_hashid'));
    }


    /**
     * @test
     */
    #[Test]
    public function the_decode_hashid_method_can_decode()
    {

        $this->assertIsInt(decode_hashid('lejRej'));
        $this->assertEquals(decode_hashid('lejRej'), 1);
    }


    /**
     * @test
     */
    #[Test]
    public function the_decode_hashid_method_on_failed_decode_return_original()
    {

        $this->assertEquals(decode_hashid(12), 12);
    }


    /**
     * @test
     */
    #[Test]
    public function the_decode_hashids_method_can_decode_one_or_multipe()
    {

        $this->assertIsInt(decode_hashids('lejRej'));
        $this->assertEquals(decode_hashids('lejRej'), 1);

        $this->assertIsArray(decode_hashids(['lejRej', 'mbk5ez']));
        $this->assertEquals(decode_hashids(['lejRej', 'mbk5ez']), [1,2]);
    }


    /**
     * @test
     */
    #[Test]
    public function the_decode_hashid_method_uses_the_configured_alphabets()
    {

        $alphabets = 'abcdefghijklmnopqrstuvwxyz';

        config(['hasher.alphabets' => $alphabets]);

        $hash = (new Hasher(config('hasher.key'), config('hasher.padding'), $alphabets))->encode(15);

        $this->assertSame(15, decode_hashid($hash));
        $this->assertSame([15, 15], decode_hashids([$hash, $hash]));
    }

}

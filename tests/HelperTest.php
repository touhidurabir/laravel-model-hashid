<?php

namespace Touhidurabir\ModelHashid\Tests;

use Orchestra\Testbench\TestCase;
use Touhidurabir\ModelHashid\Tests\Traits\LaravelTestBootstrapping;

class HelperTest extends TestCase {

    use LaravelTestBootstrapping;

    /**
     * @test
     */
    public function the_decode_hashids_method_is_callable() {

        $this->assertTrue(is_callable('decode_hashids'));
    }


    /**
     * @test
     */
    public function the_decode_hashid_method_is_callable() {

        $this->assertTrue(is_callable('decode_hashid'));
    }
    
    
    /**
     * @test
     */
    public function the_decode_hashid_method_can_decode() {

        $this->assertIsInt(decode_hashid('lejRej'));
        $this->assertEquals(decode_hashid('lejRej'), 1);
    }


    /**
     * @test
     */
    public function the_decode_hashid_method_on_failed_decode_return_original() {

        $this->assertEquals(decode_hashid(12), 12);
    }

    
    /**
     * @test
     */
    public function the_decode_hashids_method_can_decode_one_or_multipe() {

        $this->assertIsInt(decode_hashids('lejRej'));
        $this->assertEquals(decode_hashids('lejRej'), 1);

        $this->assertIsArray(decode_hashids(['lejRej', 'mbk5ez']));
        $this->assertEquals(decode_hashids(['lejRej', 'mbk5ez']), [1,2]);
    }

}
<?php

namespace Touhidurabir\ModelHashid\Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Touhidurabir\ModelHashid\Hasher\Hasher;

class HasherTest extends TestCase {
    
    /**
     * @test
     */
    public function it_can_be_initialized() {

        $hasher = new Hasher;

        $this->assertIsObject($hasher);
        $this->assertTrue($hasher instanceof Hasher);
    }


    /**
     * @test
     */
    public function it_can_be_initialized_with_key_and_or_padding() {

        $hasher = new Hasher('', 6);
        $this->assertIsObject($hasher);
        $this->assertTrue($hasher instanceof Hasher);

        $hasher = new Hasher('Some Key');
        $this->assertIsObject($hasher);
        $this->assertTrue($hasher instanceof Hasher);

        $hasher = new Hasher('Some Key', 6);
        $this->assertIsObject($hasher);
        $this->assertTrue($hasher instanceof Hasher);
    }


    /**
     * @test
     */
    public function it_can_have_the_key_set_or_update_after_initialize() {

        $hasher = new Hasher();
        $this->assertEquals($hasher->getKey(), '');

        $hasher->setKey('abcd1234');
        $this->assertEquals($hasher->getKey(), 'abcd1234');
    }


    /**
     * @test
     */
    public function it_can_have_the_padding_set_or_update_after_initialize() {

        $hasher = new Hasher();
        $this->assertNull($hasher->getPadding());

        $hasher->setPadding(6);
        $this->assertEquals($hasher->getPadding(), 6);
    }


    /**
     * @test
     */
    public function it_can_have_the_alphabets_set_or_update_after_initialize() {

        $hasher = new Hasher();
        $this->assertEquals($hasher->getAlphabets(), 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

        $hasher->setAlphabets('abcd1234xyzXYZMNOPqrst');
        $this->assertEquals($hasher->getAlphabets(), 'abcd1234xyzXYZMNOPqrst');
    }


    /**
     * @test
     */
    public function it_throws_exception_if_alphabet_less_that_16_characters() {

        $this->expectException(Exception::class);

        (new Hasher())->setAlphabets('abcd1234');
    }


    /**
     * @test
     */
    public function it_can_hash_a_number() {

        $hasher = new Hasher;

        $this->assertIsString($hasher->encode(1));
    }


    /**
     * @test
     */
    public function it_can_dehash_a_string_to_number() {

        $hasher = new Hasher;

        $this->assertEquals($hasher->decode('jR'), 1);
        $this->assertIsInt($hasher->decode('jR'));
    }

    /**
     * @test
     */
    public function it_will_throw_exception_given_non_numeric_value_to_encode() {

        $this->expectException(InvalidArgumentException::class);

        (new Hasher)->encode('some non numeric value');
    }


    /**
     * @test
     */
    public function it_will_throw_exception_given_negative_numeric_value_to_encode() {

        $this->expectException(InvalidArgumentException::class);

        (new Hasher)->encode(-1);
    }


    /**
     * @test
     */
    public function it_can_generate_hash_for_given_padding() {

        $hasher = new Hasher('Some Key', 6);

        $this->assertGreaterThanOrEqual(strlen($hasher->encode(1)), 6);
    }


    /**
     * @test
     */
    public function it_generate_hash_string_consist_of_given_alphabets_only() {

        $alphabets = 'abcd1234xyzXYZMNOPqrst';

        $hash = (new Hasher('', 4, $alphabets))->encode(1);
        
        $this->assertEquals( array_intersect(str_split($hash), str_split($alphabets)), str_split($hash) );
    }


    /**
     * @test
     */
    public function it_can_randomize_the_hash() {

        $hasher = new Hasher('Some Key', 6);

        $this->assertGreaterThanOrEqual(strlen($hasher->withRandomize(4)->encode(1)), 10);
    }

    
    /**
     * @test
     */
    public function it_can_decode_a_randomized_hash() {

        $hasher = new Hasher('Key', 8);

        $hash = $hasher->withRandomize(4)->encode(10);

        $this->assertEquals($hasher->withRandomize(4)->decode($hash), 10);
    }

    
    /**
     * @test
     */
    public function it_returns_null_on_failed_hash_decode() {

        $hasher = new Hasher('Key', 8);
        
        $this->assertNull($hasher->decode('sdfhjdsgds'));
    }
}
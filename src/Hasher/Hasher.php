<?php

namespace Touhidurabir\ModelHashid\Hasher;

use Exception;
use InvalidArgumentException;
use Hashids\Hashids;
use Illuminate\Support\Str;

class Hasher {

    /**
     * The unique key to hash
     *
     * @var string
     */
    protected $key;


    /**
     * The hash padding length
     *
     * @var int
     */
    protected $padding;


    /**
     * The hash allowed alphabets characters
     *
     * @var string
     */
    protected $alphabets;


    /**
     * extra padding length of hash id
     *
     * @var int
     */
	protected $hashRandomizePadingLength;


	/**
     * should randomize hash ids by adding extra padding
     *
     * @var boolean
     */
	protected $hashRandomize = false;


    /**
     * Create a new Hasher instance
     *
     * @param  string           $key
     * @param  mixed<int|null>  $padding
     * @param  string           $alphabets
     * 
     * @return void
     */
    public function __construct(string  $key        = '', 
                                int     $padding    = null, 
                                string  $alphabets  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890') {

        $this->key          = $key;
        $this->padding      = $padding;
        $this->alphabets    = $this->ensureIsValidHashAlphabets($alphabets);
    }


    /**
     * set the hash key
     *
     * @param  string $key
     * @return $this
     */
    public function setKey(string $key = '') {

        $this->key = $key;

        return $this;
    }


    /**
     * Get the hashing key
     *
     * @return string
     */
    public function getKey() {

        return $this->key;
    }


    /**
     * Get the hash padding
     *
     * @param  mixed<string|null> $padding
     * @return $this
     */
    public function setPadding(string $padding = null) {

        $this->padding = $padding;

        return $this;
    }


    /**
     * Get the hash padding
     *
     * @return mixed<string|null>
     */
    public function getPadding() {

        return $this->padding;
    }


    /**
     * Set the hash allowed string chars list
     *
     * @param  string $alphabets
     * @return $this
     */
    public function setAlphabets(string $alphabets) {

        $this->alphabets = $this->ensureIsValidHashAlphabets($alphabets);

        return $this;
    }


    /**
     * Set the hash allowed string chars list
     *
     * @return string
     */
    public function getAlphabets() {

        return $this->alphabets;
    }


    /**
     * Confirm the runnign of randomize algorithm on generated hash
     *
     * @param  int $padding
     * @return $this
     */
    public function withRandomize(int $padding) {

        $this->hashRandomize = true;

        $this->hashRandomizePadingLength = $padding;

        return $this;
    }
    

    /**
     * Get the encoded hash string
     *
     * @param  mixed<int|string> $hashable
     * @return string
     */
    public function encode($hashable) {

        $this->ensureIsValidHashableParam($hashable);

        return $this->applyRandomize(
            $this->gethashId()->encode($hashable)
        );
    }


    /**
     * Get the hash decoded value
     *
     * @param  string $hash
     * @return mixed<int|string>
     */
    public function decode(string $hash) {

        return $this->gethashId()
                    ->decode(
                        $this->removeRandomize($hash)
                    )[0] ?? null;

    }


    /**
     * Get an instance of \Hashids\Hashids
     *
     * @return object<\Hashids\Hashids>
     */
    protected function getHashId() {

        return (new Hashids($this->key, $this->padding, $this->alphabets));
    }


    /**
     * Determine and if albe to, Apply randomize algorithm
     *
     * @param  string $hash
     * @return string
     */
    protected function applyRandomize(string $hash) {

        if ( ! $this->hashRandomize ) {

            return $hash;
        }

        return $hash . Str::random($this->hashRandomizePadingLength);
    }


    /**
     * Determine and if needed to, remove randomized part of hashed string
     *
     * @param  string $hash
     * @return string
     */
    protected function removeRandomize(string $hash) {

        if ( ! $this->hashRandomize ) {

            return $hash;
        }

        return substr($hash, 0, $this->hashRandomizePadingLength * (-1));
    }


    /**
     * Ensure that the given hashable param is a valid one[non-negative numeric]
     *
     * @param  mixed<int|string> $hashable
     * @return void
     * 
     * @throws InvalidArgumentException
     */
    protected function ensureIsValidHashableParam($hashable) {

        if ( !is_numeric($hashable) || ($hashable < 0) ) {
			
            throw new InvalidArgumentException('The hashable must be a non negative numeric value');
		}
    }


    /**
     * Ensure that the given hash alphabets is valid
     *
     * @param  string $alphabets
     * @return string
     * 
     * @throws Exception
     */
    protected function ensureIsValidHashAlphabets(string $alphabets) {

        if ( strlen($alphabets) < 16 ) {

            throw new Exception('Alphabets must be 16 characters long');
        }

        $splitAlphabets = str_split($alphabets);
        $uniqueSplitAlphabets = array_unique($splitAlphabets);

        if ( count($splitAlphabets) !== count($uniqueSplitAlphabets) ) {

            throw new Exception('Alphabets must contains unique characters set, no duplicaion allowed');
        }

        return $alphabets;
    }

}
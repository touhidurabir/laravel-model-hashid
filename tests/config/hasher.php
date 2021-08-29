<?php

return [

	/*
    |---------------------------------------------------------------------------
    | Should the Hashing Enables
    |---------------------------------------------------------------------------
    | The configuratin of the .env to determine should the ID Hashing enabled 
    | or not for any model that used this package.
    |
    */
	'enable' => env('ID_HASHING', true),


    /*
    |---------------------------------------------------------------------------
    | Unique Key used for Hashing
    |---------------------------------------------------------------------------
    | The unique key to use for all ID hashing and then dehashing . It is advised
    | to set one such.
    |
    */
	'key' => env('ID_HASHING_KEY', ''),


	/*
    |----------------------------------------------------------------------------
    | The Hashable model column
    |----------------------------------------------------------------------------
    | Determine which column of the model table should be hashbale. This can
    | also override form each of the mode. 
    |
    */
	'column' => 'hash_id',


	/*
    |----------------------------------------------------------------------------
    | The Hashable string min length
    |----------------------------------------------------------------------------
    | This padding determine what would be the minimun lenght/padding for each
    | of hash string . It can be bigger than this padding but not less .
    |
    */
	'padding' => env('ID_HASHING_PADDING', 6),


    /*
    |----------------------------------------------------------------------------
    | The allowed characters for hashing
    |----------------------------------------------------------------------------
    | This defined the only characters that will be present in a hash string. Best
    | To have along range of characters which by default is lower case a-z with 
    | upper case A-Z and 0-9. 
    |
    | NOTE : it must be at least 16 characters long and must only contains unique
    | characters. no duplicate allowed like 'aaaabbbbbbcc' etc . 
    |
    */
	'alphabets' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',


	/*
    |-----------------------------------------------------------------------------
    | Randomize to Maximize Security
    |-----------------------------------------------------------------------------
    | The following configs details the abilityt to randomize the each hash string
    | so that only give application can dehash it .
    |
    */
	'security' => [
		/*
	    |--------------------------------------------------------------------------
	    | Randomize hashed string
	    |--------------------------------------------------------------------------
	    | Should the hash string be randomised before stored in the database.
	    |
	    */
		'randomize' => false,

        
		/*
	    |--------------------------------------------------------------------------
	    | Randomize padding
	    |--------------------------------------------------------------------------
	    | The length it will be randomize padding to be applied.
	    | 
	    |
	    */
		'padding' => 4,
	],
];
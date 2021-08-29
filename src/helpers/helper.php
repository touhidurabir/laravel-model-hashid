<?php

use Touhidurabir\ModelHashid\Hasher\Hasher;

/**
 * Generate the ids array from a array of hash ids
 *
 * @param  mixed<int|string|array> $id
 * @return mixed<String|Array>
 */
if ( ! function_exists('decode_hashids') ) {

	function decode_hashids($hashids) {

		if ( is_numeric($hashids) || is_string($hashids) ) {

			return decode_hashid($hashids);
		}

		if ( is_array($hashids) && !empty($hashids) ) {

			$ids = [];

			foreach ($hashids as $key => $hashid) {
				$ids[$key] = decode_hashid($hashid);
			}

			return $ids;
		}

		return $hashids;
	}
}


/**
 * Generate the id string of a given hash id string
 *
 * @param  mixed<int|string> $id
 * @return String
 */
if ( ! function_exists('decode_hashid') ) {

	function decode_hashid($hashid = null) {

		if ( ! $hashid ) { return null; }

		$configs = config('hasher');

		$hasher = new Hasher($configs['key'] ?? '', $configs['padding']);

		if ( $configs['security']['randomize'] ) {

			$hasher = $hasher->withRandomize($configs['security']['padding']);
		}

		return $hasher->decode($hashid) ?? $hashid;
	}
}
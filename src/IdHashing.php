<?php

namespace Touhidurabir\ModelHashid;

trait IdHashing {

	/**
     * The the passable id of this resource
     *
     * @return mixed<int|string>
     */
    public function getId() {

		if ( config('hasher.enable') ) {

			return $this->getHashId();
		}

		return $this->id;
	}
}
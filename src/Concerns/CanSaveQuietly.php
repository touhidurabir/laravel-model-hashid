<?php

namespace Touhidurabir\ModelHashid\Concerns;

trait CanSaveQuietly {

	/**
     * Save the model without firing any model events
     *
     * @param array $options
     * @return mixed
     */
	public function saveModelQuietly(array $options = []) {

	    return static::withoutEvents(function () use ($options) {
	        return $this->save($options);
	    });
	}


    /**
     * Update the model without firing any model events
     *
     * @param array $attributes
     * @param array $options
     *
     * @return mixed
     */
    public function updateModelQuietly(array $attributes = [], array $options = [])
    {
        return static::withoutEvents(function () use ($attributes, $options) {
            return $this->update($attributes, $options);
        });
    }
}
<?php

namespace Touhidurabir\ModelHashid;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Touhidurabir\ModelHashid\Hasher\Hasher;
use Touhidurabir\ModelHashid\Concerns\CanSaveQuietly;

trait IdHashable {

    use CanSaveQuietly;

    /**
     * The hasher instance
     *
     * @var object<\Touhidurabir\ModelHashid\Hasher\Hasher>
     */
    public $hasher;
    

	/**
     * hash id field column name
     *
     * @var string
     */
	public $hashIdFieldName;


    /**
     * Get the hash id column field name
     *
     * @return string
     */
	public function getHashIdFieldName() {
        
        return $this->hashIdFieldName;
    }


    /**
     * Get the original auto incrementing id column value if need
     *
     * @return mixed<int|string>
     */
	public function getId() {

        return $this->attributes['id'];
    }


    /**
     * Get the hash id
     *
     * @return string
     */
	public function getHashId() {

        if ( ! config('hasher.enable') ) {

            return $this->id;
        }

        return $this->attributes[$this->getHashIdFieldName()];
    }


    /**
     * Get the original id extracted from hash id
     *
     * @return string
     */
    public function getOriginalIdFromHashId() {

        if ( ! config('hasher.enable') ) {

            return $this->id;
        }

        $hash = $this->getHashId();

        return $hash ? $this->hasher->decode($hash) : $this->id;
    }


    /**
     * Generate hash id string
     *
     * @param  mixed<int|string> $id
     * @return string
     */
    public function generateHashId($id) {

        return $this->hasher->encode($id);
    }

	
	/**
     * Attach hash id to model object
     *
     * @return void
     */
	public static function bootIdHashable() {

        $self = new self;

		$self->initializeIdHashable();

		static::created(function($model) use ($self) {
            
            $hashIdFieldName  = $self->getHashIdFieldName();
            
            if ( Schema::hasColumn($model->getTable(), $hashIdFieldName) ) {
				
				$model->{$hashIdFieldName}
					?: $model->{$hashIdFieldName} = $self->generateHashId($model->getId());

                method_exists($self, 'saveQuietly') ? $model->saveQuietly() : $model->saveModelQuietly();
			}
        });
	}


    /**
     * constarin result by hash id
     *
     * Local Scope Implementation
     *
     * @param  Builder              $builder
     * @param  mixed<string|array>  $hashId
     *
     * @return Builder
     */
    public function scopeByHashId(Builder $builder, $hashId) {

        $method = is_array($hashId) ? 'whereIn' : 'where';

        return $builder->{$method}($this->getHashIdFieldName(), $hashId);
    }


    /**
     * Return matching model object by hash id
     *
     * @param  mixed<string|array>   $hashId
     * @return object
     */
    public static function findByHashId($hashId) {

        if ( is_array($hashId) ) {

            return static::whereIn((new self)->getHashIdFieldName(), $hashId)->get();
        }
        
        return static::byHashId($hashId)->firstOrFail();
    }


    /**
     * Get user over written configs if provided
     *
     * @return void
     */
	protected function initializeIdHashable() {

        $hashColumn = null;

		if (method_exists($this, 'getHashColumn') ) {

            $hashColumn = $this->getHashColumn();
        }

        $this->hashIdFieldName = is_string($hashColumn) ? $hashColumn : config('hasher.column');

        $this->hasher = new Hasher(config('hasher.key') ?? '', config('hasher.padding'), config('hasher.alphabets'));

        if ( config('hasher.security.randomize') ) {

            $this->hasher = $this->hasher->withRandomize(config('hasher.security.padding'));
        }
	}
}
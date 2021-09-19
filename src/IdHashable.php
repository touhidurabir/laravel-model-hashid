<?php

namespace Touhidurabir\ModelHashid;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Touhidurabir\ModelHashid\Hasher\Hasher;
use Touhidurabir\ModelHashid\Concerns\CanSaveQuietly;

trait IdHashable {

    use CanSaveQuietly;

    /**
     * Should disbale hashId generation for this model
     *
     * @var bool
     */
    public static $disbaleHashIdGeneration = false;


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
     * Disable/Enable hash id generation for model
     *
     * @param  bool $state
     * @return void
     */
    public static function disbaleHashIdGeneration(bool $state = true) {

        static::$disbaleHashIdGeneration = $state;
    }


    /**
     * Can model have hashid associated with it
     *
     * @param  mixed<string|null> $table
     * @param  mixed<string|null> $column
     * 
     * @return bool
     */
    public function canHaveHashId(string $table = null, string $column = null) {

        return Schema::hasColumn($table ?? $this->getTable(), $column ?? $this->getHashColumnName());
    }


    /**
     * Get the hash id column field name
     *
     * @return string
     */
	public function getHashIdFieldName() {
        
        return $this->hashIdFieldName ?? $this->getHashColumnName();
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

        return $this->attributes[$this->getHashIdFieldName()] ?? null;
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

        if ( ! $this->hasher ) {

            $this->hasher = $this->getConfiguredHasher();
        }

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

            if ( $self::$disbaleHashIdGeneration ) {

                return;
            }
            
            $hashIdFieldName  = $self->getHashIdFieldName();

            if ( ! $self->canHaveHashId($model->getTable(), $hashIdFieldName) ) {

                return;
            }
            
            $model->{$hashIdFieldName}
                ?: $model->{$hashIdFieldName} = $self->generateHashId($model->getId());

            method_exists($self, 'saveQuietly') ? $model->saveQuietly() : $model->saveModelQuietly();
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
	public function initializeIdHashable() {

        $this->hashIdFieldName = $this->getHashColumnName();

        $this->hasher = $this->getConfiguredHasher();
	}


    /**
     * Get the model/table associated hash column name
     *
     * @return string
     */
    protected function getHashColumnName() {

        $hashColumn = null;

        if (method_exists($this, 'getHashColumn') ) {

            $hashColumn = $this->getHashColumn();
        }

        return is_string($hashColumn) ? $hashColumn : config('hasher.column');
    }


    /**
     * Get the configured hasher instance based on given configs
     *
     * @return object<\Touhidurabir\ModelHashid\Hasher\Hasher>
     */
    protected function getConfiguredHasher() {

        $hasher = new Hasher(config('hasher.key') ?? '', config('hasher.padding'), config('hasher.alphabets'));

        if ( config('hasher.security.randomize') ) {

            $hasher = $hasher->withRandomize(config('hasher.security.padding'));
        }

        return $hasher;
    }
    
}
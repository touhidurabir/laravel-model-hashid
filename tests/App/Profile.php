<?php

namespace Touhidurabir\ModelHashid\Tests\App;

use Touhidurabir\ModelHashid\IdHashable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model {

    use SoftDeletes;

    use IdHashable;

    /**
     * The model associated table
     *
     * @var string
     */
    protected $table = 'profiles';


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the name of hash column name
     *
     * @return string
     */
    public function getHashColumn() {

        return 'hash';
    }

}
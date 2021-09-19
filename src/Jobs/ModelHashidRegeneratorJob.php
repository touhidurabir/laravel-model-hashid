<?php

namespace Touhidurabir\ModelHashid\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ModelHashidRegeneratorJob implements ShouldQueue {

    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The target models class
     *
     * @var string
     */
    public $models;


    /**
     * Should apply the HashId on all existing records
     *
     * @var bool
     */
    public $updateAll = false;


    /**
     * Create a new job instance.
     *
     * @param  array $models
     * @param  bool $updateAll
     * 
     * @return void
     */
    public function __construct(array $models, bool $updateAll) {

        $this->models       = $models;
        $this->updateAll    = $updateAll;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        foreach($this->models as $model) {

            $traits = class_uses_recursive($model);

            if ( ! in_array('Touhidurabir\\ModelHashid\\IdHashable', array_values($traits)) ) {

                continue;
            }

            $instance = new $model;
            
            if ( ! $instance->canHaveHashId() ) {

                continue;
            }

            $hashColumn = $instance->getHashIdFieldName();

            $records = $instance->select(['id', $hashColumn]);

            $records = $this->updateAll ? $records : $records->whereNull($hashColumn);

            $records->chunk(5000, function ($rows) use ($hashColumn, $instance) {
                $rows->each(function ($row) use ($hashColumn, $instance) {
                    $row->update([
                        $hashColumn => $instance->generateHashId( $row->id )
                    ]);
                });
            });
        }
    }
    
}
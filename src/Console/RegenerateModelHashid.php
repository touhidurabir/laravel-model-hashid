<?php

namespace Touhidurabir\ModelHashid\Console;

use Exception;
use Throwable;
use Illuminate\Console\Command;
use Touhidurabir\ModelHashid\Console\Concerns\CommandExceptionHandler;

class RegenerateModelHashid extends Command {

    /**
     * Process the handeled exception and provide output
     */
    use CommandExceptionHandler;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashid:run
                            {models         : Only for given/specified model resource}
                            {--update-all   : Rather then only for the missing ones, update all}
                            {--on-job       : Run the regeration process via a queue job}
                            {--job=         : The provided queue job class full namespace path}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the hash id regeneration process for the given model/s';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info('Initiating hash id regenration process');

        try {
            
            if ( empty(
                $models = $this->buildFullModelsNamespace( explode(',', $this->argument('models')) )) 
            ) {

                return $this->handleFailure('No model has provided');
            }

            if ( ! class_exists($job = $this->option('job') ?? config('hasher.regeneration_job')) ) {

                return $this->handleFailure('Job class not found');
            }

            $this->option('on-job') 
                ? $job::dispatch($models, $this->option('update-all')) 
                : $job::dispatchNow($models, $this->option('update-all'));
            
        } catch (Throwable $exception) {
            
            $this->outputConsoleException($exception);

            return 1;
        }
    }


    /**
     * Build up the proper model namespaces
     *
     * @param  array $models
     * @return array
     */
    protected function buildFullModelsNamespace(array $models = []) {
        
        $models = array_map('trim', array_filter(array_unique($models)));
        
        foreach ($models as $index => $model) {

            if ( $this->hasNamespaceAssociatedWith($model) ) {

                continue;
            }

            $models[$index] = 'App\\Models\\' . $model;
        }

        return array_filter($models, function ($model) {
            return class_exists($model);
        });
    }


    /**
     * Resolve the class namespace from given class name
     *
     * @param  string $name
     * @return mixed<string|null>
     */
    public function hasNamespaceAssociatedWith(string $name) {

        $classFullNameExplode = explode('\\', $name);

        if ( count($classFullNameExplode) <= 1 ) {

            return false;
        }

        return true;
    }


    /**
     * Handle some basic failure case
     *
     * @param  string $message
     * @param  int    $return 
     * 
     * @return int
     */
    protected function handleFailure(string $message, int $return = 1) {
        
        $this->error($message) ;

        return $return;
    }
    
}
<?php


namespace RadiateCode\DaStats;

use RadiateCode\DaStats\Stores\DatabaseStatsStore;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class StatsManager
{
    protected $driver;

    public function __construct()
    {
        $this->driver = $this->getDefaultDriver();
    }

    /**
     * Resolve storage based on config
     *
     * @return mixed
     */
    public function stores()
    {
        return $this->resolveStorage();
    }

    /**
     * Create model instance
     *
     * @return mixed|null
     */
    private function createModel()
    {
        $model = $this->getModel();

        $class = '\\'.ltrim($model, '\\');

        $model = new $class;

        if ($model instanceof Model){
            return $model;
        }

        throw new InvalidArgumentException(
            "Given model class is not an instance of [Illuminate\Database\Eloquent\Model]"
        );
    }

    /**
     * Get the driver name.
     *
     * @return string
     */
    private function getDefaultDriver(): string
    {
        return 'database';
    }

    /**
     * @return mixed
     */
    private function resolveStorage()
    {
        $storageMethod = "create".ucfirst($this->driver)."Storage";

        if(method_exists($this,$storageMethod)){
            return $this->{$storageMethod}();
        }

        throw new InvalidArgumentException(
            "In dastats config storage driver [{$this->driver}] is not defined or is not supported."
        );
    }

    /**
     * Database storage
     *
     * @return DatabaseStatsStore
     */
    private function createDatabaseStorage(){
        return new DatabaseStatsStore($this->createModel());
    }

    /**
     * Get the model.
     *
     * @return string
     */
    private function getModel(): string
    {
        $model = config('dastats.storage.database');
    
        if (array_key_exists('model',$model)){
            return $model['model'];
        }

        throw new InvalidArgumentException(
            "In the dastats config file [model] key is not defined for database storage driver."
        );
    }
}
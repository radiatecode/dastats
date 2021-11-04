<?php

namespace RadiateCode\DaStats;

use InvalidArgumentException;

class Stats
{
    /**
     * @var $storage
     */
    private $storage;

    public function __construct()
    {
        $this->storage = $this->resolve();
    }

    /**
     * Isolate stats
     *
     * @param  string  $name
     * @param  int  $id
     *
     * @return $this
     */
    public function isolate(string $name,int $id): Stats
    {
        $this->storage->isolate($name,$id);

        return $this;
    }

    /**
     *
     * @param  string  $title
     *
     * @return $this
     */
    public function title(string $title): Stats
    {
        $this->storage->title($title);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function key(string $key): Stats
    {
        $this->storage->key($key);

        return $this;
    }

    /**
     * @param  int  $value
     *
     * @return bool
     */
    public function increase(int $value = 1): bool
    {
        return $this->storage->increase($value);
    }

    /**
     * @param  int  $value
     *
     * @return bool
     */
    public function decrease(int $value = 1): bool
    {
        return $this->storage->decrease($value);
    }

    /**
     * @param  int  $value
     *
     * @return bool
     */
    public function replace(int $value): bool
    {
        return $this->storage->replace($value);
    }

    /**
     * Do many increase or decrease at a time
     *
     * @param  string  $action // StatsAction::INCREASE or StatsAction::DECREASE
     *
     * @param  array  $data
     *
     * @return bool
     *
     * -----------------------------------------
     * $action = StatsAction::INCREASE; | [ex: StatsAction::INCREASE,StatsAction::DECREASE,StatsAction::REPLACE]
     *
     * | data format
     *
     * $data = [
     *      ['key'=>1, value=120],
     *      ['key'=>3, value=1],
     *      ['key'=>11, value=23]
     * ];
     */
    public function doMany(string $action, array $data): bool
    {
        $method = strtolower($action);

        if (! method_exists($this, $method)) {
            throw new InvalidArgumentException("Invalid [{$action}] action!");
        }

        foreach ($data as $item) {
            if (array_key_exists('key', $item) && array_key_exists('value', $item)) {
                $this->key($item['key']);

                $this->{$method}($item['value']);
            }
        }

        return true;
    }

    /**
     * @param  string  $table
     * @param  string  $pk
     * @param  array  $select
     *
     * @return $this
     */
    public function join(string $table,string $pk = 'id',array $select = []): Stats
    {
        $this->storage->join($table,$pk,$select);

        return $this;
    }

    /**
     * @return $this
     */
    public function inKeys(...$key): Stats
    {
        $this->storage->inKeys($key);

        return $this;
    }

    /**
     * find stats which contains given key
     *
     * [nt: $key could be a part of key]
     *
     * @return $this
     */
    public function contain(string $key): Stats
    {
        $this->storage->contain($key);

        return $this;
    }

    /**
     * @return mixed
     */
    public function find(){
        return $this->storage->find();
    }

    /**
     * @param  int  $perPage
     *
     * @return mixed
     */
    public function paginate(int $perPage = 10)
    {
        return $this->storage->paginate($perPage);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->storage->get();
    }

    /**
     *
     * @return bool
     */
    public function remove(): bool
    {
        return $this->storage->remove();
    }

    /**
     * Apply the callbacks based on value
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  callable|null  $default
     *
     * @return $this|mixed
     */
    public function when(bool $value, callable $callback, callable $default = null): Stats
    {
        if ($value) {
            return $callback($this) ?: $this;
        } elseif ($default) {
            return $default($this) ?: $this;
        }

        return $this;
    }

    /**
     * Check stats exists or not
     *
     * @param  callable|null  $existsCallback // if exists then do this
     * @param  callable|null  $notExistsCallback // if not exists then do this
     *
     * @return bool|Stats
     */
    public function exists(callable $existsCallback = null, callable $notExistsCallback = null){
        $stats = $this->storage->find();

        if ($existsCallback && $stats){
            return $existsCallback($this);
        }elseif ($existsCallback && $notExistsCallback && !$stats) {
            return $notExistsCallback($this);
        }

        return ! empty($stats);
    }

    /**
     * Resolve storage
     */
    private function resolve()
    {
        return (new StatsManager())->stores();
    }
}

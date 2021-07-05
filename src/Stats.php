<?php

namespace DaCode\DaStats;

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
     * @return mixed
     */
    public function increment()
    {
        return $this->storage->increment();
    }

    /**
     * @return mixed
     */
    public function decrement()
    {
        return $this->storage->decrement();
    }

    /**
     * @param  int  $value
     *
     * @return mixed
     */
    public function addition(int $value)
    {
        return $this->storage->addition($value);
    }

    /**
     * @param  int  $value
     *
     * @return mixed
     */
    public function subtraction(int $value)
    {
        return $this->storage->subtraction($value);
    }

    /**
     * @param ...$titles
     *
     * @return $this
     */
    public function statsByTitles(...$titles): Stats
    {
        $this->storage->statsByTitles($titles);

        return $this;
    }

    /**
     * @return $this
     */
    public function statsByKeys(...$key): Stats
    {
        $this->storage->statsByKeys($key);

        return $this;
    }

    /**
     * @param  string  $type
     *
     * @return $this
     */
    public function statsByType(string $type): Stats
    {
        $this->storage->statsByType($type);

        return $this;
    }

    /**
     * @param  string  $key
     *
     * @return $this
     */
    public function contains(string $key): Stats
    {
        $this->storage->contains($key);

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
     * @param  string  $table
     * @param  string  $pk
     * @param  array  $select
     *
     * @return $this
     */
    public function join(string $table,string $pk,array $select = []): Stats
    {
        $this->storage->join($table,$pk,$select);

        return $this;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function remove($id = null){
        return $this->storage->removeStats($id);
    }

    /**
     * Apply the callbacks based on value
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  callable|null  $default
     *
     * @return $this|Stats|mixed
     */
    public function when(bool $value, callable $callback, callable $default = null)
    {
        if ($value) {
            return $callback($this) ?: $this;
        } elseif ($default) {
            return $default($this) ?: $this;
        }

        return $this;
    }

    /**
     * Resolve storage
     */
    private function resolve()
    {
        return (new StatsManager())->stores();
    }
}

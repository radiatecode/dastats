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
    public function increaseOrReplace(int $value): bool
    {
        return $this->storage->increaseOrReplace($value);
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
     * @return $this
     */
    public function inKeys(...$key): Stats
    {
        $this->storage->inKeys($key);

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
    public function join(string $table,string $pk = 'id',array $select = []): Stats
    {
        $this->storage->join($table,$pk,$select);

        return $this;
    }

    /**
     * @param int|null $id
     *
     * @return bool
     */
    public function remove(int $id = null): bool{
        return $this->storage->remove($id);
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
     * Resolve storage
     */
    private function resolve()
    {
        return (new StatsManager())->stores();
    }
}

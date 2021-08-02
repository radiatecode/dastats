<?php


namespace RadiateCode\DaStats\Stores;

use RadiateCode\DaStats\Contracts\StatsInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class DatabaseStatsStore implements StatsInterface
{
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $key = '';

    /**
     * @var bool
     */
    private $isolate = false;

    /**
     * @var null
     */
    private $isolation_id = null;

    /**
     * @var null
     */
    private $isolation_name = null;

    /**
     * @var Model $model
     */
    private $model = null;

    /**
     * @var Builder
     */
    private $query = null;

    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->query = $this->model->newQuery();
    }

    /**
     * Isolate a stats by [ex: Specific Organisation, Tenant, User]
     *
     * @param  string  $name
     * @param  int  $id
     *
     * @return $this
     */
    public function isolate(string $name, int $id): StatsInterface
    {
        $this->isolate = true;

        $this->isolation_id = $id;

        $this->isolation_name = $name;

        return $this;
    }

    /**
     * Title of the stats
     *
     * @param  string  $title
     *
     * @return $this
     */
    public function title(string $title): StatsInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set key for uniquely identify the stats
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function key(string $key): StatsInterface
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Add stats value
     *
     * @param  int  $value
     *
     * @return bool
     */
    public function increase(int $value): bool
    {
        $this->metaDataException();

        if ($value <= 0) {
            throw new InvalidArgumentException('Value should be greater than 0!');
        }

        $stats = $this->find();

        if (empty($stats)) {
            $this->create($value);

            return true;
        }

        $stats->update(['value' => (int) $stats->value + $value]);

        return true;
    }


    /**
     * Subtract stats value
     *
     * @param  int  $value
     *
     * @return bool
     */
    public function decrease(int $value): bool
    {
        $this->metaDataException();

        $stats = $this->find();

        if (empty($stats)) {
            return false;
        }

        if ($stats->value < $value || $value < 0) {
            throw new InvalidArgumentException('Subtract value is Out of Bounds!');
        }

        $decrement = (int) $stats->value - $value;

        if ($decrement == 0) {
            $stats->delete();

            return true;
        }

        $stats->update(['value' => $decrement]);

        return true;
    }

    /**
     *
     * @return bool
     */
    public function remove(): bool
    {
        return $this->query()->delete();
    }

    /**
     * @param  string  $table
     * @param  string  $pk
     * @param  array  $select
     *
     * @return $this
     */
    public function join(string $table, string $pk = 'id', array $select = []): StatsInterface
    {
        $this->query = $this->query->join($table, $table.'.'.$pk, '=', 'da_stats.key')
            ->when(! empty($select), function ($query) use ($select) {
                return $query->select(array_merge(['da_stats.*'], $select));
            });

        return $this;
    }

    /**
     * Get stats by keys
     *
     * @param  $keys
     *
     * @return $this|mixed
     */
    public function inKeys(...$keys): StatsInterface
    {
        $keys = Arr::flatten($keys);

        $this->query = $this->query->whereIn('key', $keys);

        return $this;
    }

    /**
     * @return Collection|mixed
     */
    public function find()
    {
        $stats = $this->query()->first();

        $this->resetModelQuery(); // prevent further chaining

        return $stats;
    }

    /**
     * Get stats by paginate
     *
     * @param  int  $perPage
     *
     * @return LengthAwarePaginator|mixed
     */
    public function paginate(int $perPage)
    {
        $stats = $this->query()->orderByDesc('id')->paginate($perPage);

        $this->resetModelQuery(); // prevent further chaining

        return $stats;
    }

    /**
     * @return Collection|mixed
     */
    public function get()
    {
        $stats = $this->query()->orderByDesc('id')->get();

        $this->resetModelQuery(); // prevent further chaining

        return $stats;
    }

    /**
     * Exception
     */
    private function metaDataException()
    {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Title is not set');
        }

        if (empty($this->key)) {
            throw new InvalidArgumentException('Key is not set');
        }
    }

    /**
     * Query builder
     *
     * @return Builder
     */
    private function query(): Builder
    {
        return $this->query->when($this->isolate, function ($query) {
            return $query->where('isolation_id', $this->isolation_id);
        })->when($this->title, function ($query) {
            return $query->where('title', $this->title);
        })->when($this->key, function ($query) {
            return $query->where('key', $this->key);
        });
    }

    /**
     * reset query
     */
    private function resetModelQuery()
    {
        $this->query = $this->model->newQuery();
    }

    /**
     * @param  int  $value
     *
     * @return mixed
     */
    private function create(int $value){
        return $this->model->create(
            [
                'isolation_id' => $this->isolate ? $this->isolation_id : null,
                'isolation_name' => $this->isolate ? $this->isolation_name : null,
                'title' => $this->title,
                'key' => $this->key,
                'value' => $value,
            ]
        );
    }
}
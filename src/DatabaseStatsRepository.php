<?php


namespace DaCode\DaStats;

use DaCode\DaStats\Contracts\StatsInterface;
use DaCode\DaStats\Enum\StatsType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class DatabaseStatsRepository implements StatsInterface
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

        $this->query();
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
     * Increment for specific key
     *
     * @return bool
     */
    public function increment(): ?bool
    {
        $stats = $this->find();

        if (empty($stats)) {
            $this->model->create(
                [
                    'isolation_id'   => $this->isolation_id,
                    'isolation_name' => $this->isolation_name,
                    'title'          => $this->title,
                    'key'            => $this->key,
                    'value'          => 1,
                    'type'           => StatsType::COUNTABLE,
                ]
            );

            return true;
        }

        $stats->update(['value' => (int) $stats->value + 1]);

        return true;
    }

    /**
     * Decrement for specific stats key
     *
     * @return bool|null
     */
    public function decrement(): ?bool
    {
        $stats = $this->find();

        if (empty($stats)) {
            return false;
        }

        $decrement = (int) $stats->value - 1;

        if ($decrement == 0) {
            $stats->delete();

            return true;
        }

        $stats->update(['value' => $decrement]);

        return true;
    }

    /**
     * Addition for given key
     *
     * @return mixed
     */
    public function addition(int $value)
    {
        $this->metaDataException();

        $old = $this->find();

        if ( ! empty($old)) {
            return $old->update([
                'value' => (int) $old->value + $value,
            ]);
        }

        return $this->model->create(
            [
                'isolation_id'   => $this->isolate ? $this->isolation_id : 0,
                'isolation_name' => $this->isolate ? $this->isolation_name
                    : null,
                'key'            => $this->key,
                'title'          => $this->title,
                'value'          => $value,
                'type'           => StatsType::SUMMATION,
            ]
        );
    }

    /**
     * Subtraction from given existing stats
     *
     * @param  int  $value
     *
     * @return mixed|null
     */
    public function subtraction(int $value)
    {
        $this->metaDataException();

        $old = $this->find();

        if ( ! empty($old)) {
            $result = (int) $old->value - $value;

            if ($result == 0) {
                $old->delete();

                return null;
            }

            return $old->update([
                'value' => (int) $old->value - $value,
            ]);
        }

        return null;
    }

    /**
     * Get stats by type
     *
     * @param  string  $type
     *
     * @return $this|mixed
     */
    public function statsByType(string $type): StatsInterface
    {
        $query = $this->query ?: $this->query();

        $this->query = $query->where('type', '=', $type)->orderByDesc('id');

        return $this;
    }

    /**
     * Get stats by titles
     *
     * @param  array  $titles
     *
     * @return $this|mixed
     */
    public function statsByTitles(array $titles): StatsInterface
    {
        $query = $this->query ?: $this->query();

        $this->query = $query->whereIn('title', $titles)->orderByDesc('id');

        return $this;
    }

    /**
     * Get stats by keys
     *
     * @param  array  $keys
     *
     * @return $this|mixed
     */
    public function statsByKeys(array $keys): StatsInterface
    {
        $query = $this->query ?: $this->query();

        $this->query = $query->whereIn('key', $keys)->orderByDesc('id');

        return $this;
    }

    /**
     * Get stats by containing given key
     *
     * @param  string  $key
     *
     * @return $this|mixed
     */
    public function contains(string $key): StatsInterface
    {
        $query = $this->query ?: $this->query();

        $this->query = $query->where('key', 'like', '%'.$key.'%');

        return $this;
    }

    /**
     * @return mixed|Collection
     */
    public function find()
    {
        return $this->query()->when($this->title, function ($query) {
            return $query->where('title', $this->title);
        })->where('key', '=', $this->key)->first();
    }

    /**
     * Get stats by paginate
     *
     * @param  int  $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage)
    {
        return $this->query
            ? $this->query->orderByDesc('id')->paginate($perPage)
            : $this->query()->orderByDesc('id')->paginate($perPage);
    }

    /**
     * @return Collection|null
     */
    public function get()
    {
        return $this->query
            ? $this->query->get()
            : $this->query()->orderByDesc('id')->get();
    }

    /**
     * @param  string  $table
     * @param  string  $pk
     * @param  array  $select
     *
     * @return $this
     */
    public function join(string $table, string $pk, array $select = [])
    {
        $query = $this->query ?: $this->query();

        $this->query = $query->join($table, $table.'.'.$pk, '=', 'da_stats.key')
            ->when(! empty($select), function ($query) use ($select) {
                return $query->select(array_merge(['da_stats.*'], $select));
            });

        return $this;
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
     * @param  int|null  $id
     *
     * @return bool
     */
    public function removeStats(int $id = null): bool
    {
        return $id
            ? $this->model::findOrFail($id)->delete()
            : $this->find()->delete();
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
        $query = $this->model->newQuery();

        return $query->when($this->isolate, function ($query) {
            return $query->where('isolation_id', $this->isolation_id);
        });
    }
}
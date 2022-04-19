<?php


namespace RadiateCode\DaStats\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static \RadiateCode\DaStats\Stats isolate(string $name,int $id)
 * @method static \RadiateCode\DaStats\Stats title(string $title)
 * @method static \RadiateCode\DaStats\Stats key(string $key)
 * @method static \RadiateCode\DaStats\Stats inKeys(...$key)
 * @method static \RadiateCode\DaStats\Stats contain(string $key)
 * @method static \RadiateCode\DaStats\Stats joinWith(string $table,string $pk = 'id')
 * @method static \RadiateCode\DaStats\Stats join($table, $first, $operator = null, $second = null)
 * @method static \RadiateCode\DaStats\Stats|mixed when(bool $value, callable $callback, callable $default = null)
 * @method static mixed exists(callable $existsCallback = null,callable $notExistsCallback = null)
 * @method static mixed increase(float $value = 1)
 * @method static mixed decrease(float $value = 1)
 * @method static mixed replace(float $value)
 * @method static mixed doMany(string $action, array $data)
 * @method static mixed find()
 * @method static mixed paginate(int $perPage = 10)
 * @method static mixed get(array $columns = [])
 * @method static mixed remove()
 *
 *
 * @see \RadiateCode\DaStats\Stats
 */

class Stats extends Facade
{
    protected static function getFacadeAccessor(){
        self::clearResolvedInstance('da.stats');

        return 'da.stats';
    }
}
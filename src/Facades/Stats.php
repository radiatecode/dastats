<?php


namespace DaCode\DaStats\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static \DaCode\DaStats\Stats isolate(string $name,int $id)
 * @method static \DaCode\DaStats\Stats title(string $title)
 * @method static \DaCode\DaStats\Stats key(string $key)
 * @method static \DaCode\DaStats\Stats inKeys(...$key)
 * @method static \DaCode\DaStats\Stats join(string $table,string $pk = 'id',array $select = [])
 * @method static \DaCode\DaStats\Stats|mixed when(bool $value, callable $callback, callable $default = null)
 * @method static mixed increase(int $value = 1)
 * @method static mixed decrease(int $value = 1)
 * @method static mixed replace(int $value)
 * @method static mixed doMany(string $action, array $data): bool
 * @method static mixed find()
 * @method static mixed paginate(int $perPage = 10)
 * @method static mixed get()
 * @method static mixed remove($id = null)
 *
 *
 * @see \DaCode\DaStats\Stats
 */

class Stats extends Facade
{
    protected static function getFacadeAccessor(){
        self::clearResolvedInstance('da.stats');

        return 'da.stats';
    }
}
<?php


namespace DaCode\DaStats\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static \DaCode\DaStats\Stats isolate(string $name,int $id)
 * @method static \DaCode\DaStats\Stats title(string $title)
 * @method static \DaCode\DaStats\Stats key(string $key)
 * @method static \DaCode\DaStats\Stats statsByTitles(...$titles)
 * @method static \DaCode\DaStats\Stats statsByKeys(...$key)
 * @method static \DaCode\DaStats\Stats statsByType(string $type)
 * @method static \DaCode\DaStats\Stats contains(string $key)
 * @method static \DaCode\DaStats\Stats join(string $table,string $pk,array $select = [])
 * @method static \DaCode\DaStats\Stats|mixed when(bool $value, callable $callback, callable $default = null)
 * @method static mixed increment()
 * @method static mixed decrement()
 * @method static mixed addition(int $value)
 * @method static mixed subtraction(int $value)
 * @method static mixed find()
 * @method static mixed all()
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
        return 'da.stats';
    }
}
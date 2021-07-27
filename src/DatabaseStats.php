<?php
namespace RadiateCode\DaStats;

use Illuminate\Database\Eloquent\Model;

class DatabaseStats extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'da_stats';

    /**
     * @var array
     */
    protected $guarded = [];
}
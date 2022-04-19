<?php


namespace RadiateCode\DaStats\Jobs;

use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use Illuminate\Queue\SerializesModels;
use RadiateCode\DaStats\Facades\Stats;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SingleStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;

    public $key;

    public $value;

    public $isolation_id = null;

    public $isolation_name = null;

    public $action = null;

    /**
     * SingleStatsJob constructor.
     *
     * @param  string  $action
     * @param  string  $title
     * @param  string  $key
     * @param  float  $value
     *
     * --------------------------------------
     * $action = StatsAction::INCREASE; | [ex: StatsAction::INCREASE,StatsAction::DECREASE]
     *
     */
    public function __construct(string $action,string $title,string $key,float $value = 1)
    {
        if ( ! in_array(strtolower($action), ['increase', 'decrease', 'replace'])) {
            throw new InvalidArgumentException("Invalid [{$action}] action!");
        }

        $this->title = $title;

        $this->key = $key;

        $this->value = $value;

        $this->action = $action;
    }

    /**
     * @param  string  $isolation_name
     * @param  int  $isolation_id
     *
     * @return $this
     */
    public function withIsolation(string $isolation_name,int $isolation_id): SingleStatsJob
    {
        $this->isolation_name = $isolation_name;

        $this->isolation_id = $isolation_id;

        return $this;
    }

    /**
     * Queue job handle
     */
    public function handle()
    {
        Stats::when(! empty($this->isolation_id),function ($stats){
            return $stats->isolate($this->isolation_name,$this->isolation_id);
        })->title($this->title)->key($this->key)->{$this->action}($this->value);
    }
}
<?php


namespace RadiateCode\DaStats\Jobs;

use RadiateCode\DaStats\Facades\Stats;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class MultiStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;

    public $data;

    public $isolation_id = null;

    public $isolation_name = null;

    public $action = null;

    /**
     * MultiStatsJob constructor.
     *
     * @param  string  $action
     * @param  string  $title
     * @param  array  $data
     *
     * -----------------------------------------
     * $action = StatsAction::INCREASE; | [ex: StatsAction::INCREASE,StatsAction::DECREASE,StatsAction::REPLACE]
     *
     * $data = [
     *      ['key'=>1,value=120],
     *      ['key'=>3,value=1],
     *      ['key'=>11,value=23]
     * ];
     */
    public function __construct(string $action,string $title,array $data)
    {
        if ( ! in_array(strtolower($action), ['increase', 'decrease', 'replace'])) {
            throw new InvalidArgumentException("Invalid [{$action}] action!");
        }

        $this->title = $title;

        $this->data = $data;

        $this->action = $action;
    }

    /**
     * @param  string  $isolation_name
     * @param  int  $isolation_id
     *
     * @return $this
     */
    public function withIsolation(string $isolation_name,int $isolation_id): MultiStatsJob
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
        })->title($this->title)->doMany($this->action,$this->data);
    }
}
<?php


namespace DaCode\DaStats\Jobs;

use DaCode\DaStats\Facades\Stats;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class StatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;

    public $data;

    public $isolation_id = null;

    public $isolation_name = null;

    public $action = null;

    /**
     * @param  string  $isolation_name
     * @param  int  $isolation_id
     *
     * @return $this
     */
    public function withIsolation(string $isolation_name,int $isolation_id): StatsJob
    {
        $this->isolation_name = $isolation_name;

        $this->isolation_id = $isolation_id;

        return $this;
    }

    public function __construct(string $title,array $data,string $action)
    {
        if ( ! in_array(strtolower($action), ['increase', 'decrease', 'replace'])) {
            throw new InvalidArgumentException("Invalid [{$action}] action!");
        }

        $this->title = $title;

        $this->data = $data;

        $this->action = $action;
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
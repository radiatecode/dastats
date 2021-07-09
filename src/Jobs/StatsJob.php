<?php


namespace DaCode\DaStats\Jobs;

use DaCode\DaStats\Facades\Stats;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;

    public $data;

    public $isolation_id = null;

    public $isolation_name = null;

    public $action = null;

    public function __construct(string $title,array $data,string $action)
    {
        $this->title = $title;

        $this->data = $data;

        $this->action = $action;
    }

    /**
     * @param  int  $isolation_id
     * @param  string  $isolation_name
     *
     * @return $this
     */
    public function withIsolation(int $isolation_id,string $isolation_name): StatsJob
    {
        $this->isolation_id = $isolation_id;

        $this->isolation_name = $isolation_name;

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
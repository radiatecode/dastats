<?php


namespace RadiateCode\DaStats;


use Closure;
use RadiateCode\DaStats\Console\StatsTableCommand;
use Illuminate\Support\ServiceProvider;

class StatsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dastats.php', 'dastats');

        $this->app->bind('da.stats',function ($app){
            return $this->app->make(Stats::class);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                StatsTableCommand::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/dastats.php' => config_path('dastats.php'),
        ],'dastats-config');
    }
}
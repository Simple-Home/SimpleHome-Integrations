<?php

namespace Modules\OpenWeatherMap\Providers;

use App\Helpers\SettingManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\OpenWeatherMap\Jobs\Fetch;

class OpenWeatherMapServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'OpenWeatherMap';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'openweathermap';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerSettings();

        //(new Fetch)->handle();
        //  $schedule = $this->app->make(Schedule::class);
        //     $schedule->job(new Fetch)->withoutOverlapping()->everyMinute();

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new Fetch)->withoutOverlapping()->everyMinute();
        });
    }

    public function registerSettings()
    {
        $index = "city";
        if (SettingManager::get($index, $this->moduleNameLower) == null) {
            SettingManager::register($index, true, 'string', $this->moduleNameLower);
        }

        $index = "apiToken";
        if (SettingManager::get($index, $this->moduleNameLower) == null) {
            SettingManager::register($index, true, 'string', $this->moduleNameLower);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }
}

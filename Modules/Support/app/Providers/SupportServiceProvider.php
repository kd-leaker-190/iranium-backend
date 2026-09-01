<?php

namespace Modules\Support\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Support\Console\Log\LogClear;
use Modules\Support\Console\MyG;
use Modules\Support\Console\Service;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SupportServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Support';

    protected string $nameLower = 'support';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            Service::class,
            MyG::class,
            LogClear::class
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {

    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}

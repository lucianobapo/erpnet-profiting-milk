<?php

namespace ErpNET\Profiting\Milk\Providers;

use Illuminate\Support\ServiceProvider;

class ErpnetProfitingMilkServiceProvider extends ServiceProvider
{
    protected $commands = [
        \ErpNET\Profiting\Milk\Console\Commands\Install::class,
    ];
    
    private     $projectRootDir;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {        
        
        //Routing
        $routesDir = $this->getProjectRootDir()."routes".DIRECTORY_SEPARATOR;
//        include $routesDir."api.php";
        include $routesDir."web.php";
        
        $this->publishMigrations();
        
        $this->listenEvents();
        
        $this->registerTranslations();
        
        $this->registerViews();
        
        $this->mergeConfig();

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->register(\Dingo\Api\Provider\LaravelServiceProvider::class);

        // register the artisan commands
        $this->commands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
    
    private function publishMigrations()
    {
        $path = $this->getMigrationsPath();
        $this->publishes([$path => database_path('migrations')], 'migrations');
    }
    
    private function getProjectRootDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR;
    }
    
    private function getMigrationsPath()
    {
        return $this->getProjectRootDir() . 'database/migrations/';
    }
    
    private function getConfigPath()
    {
        return $this->getProjectRootDir() . 'config/erpnet-profiting-milk.php';
    }
    
    private function listenEvents()
    {
        $this->app['events']->listen(\App\Events\AdminMenuCreated::class, 
            \ErpNET\Profiting\Milk\Listeners\AdminMenu::class);
    }
    
    /**
     * Register translations.
     *
     * @return void
     */
    private function registerTranslations()
    {
        $langPath = $this->projectRootDir . 'resources/lang';
        
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'erpnet-profiting-milk');
        }
    }
    
    
    /**
     * Register views.
     *
     * @return void
     */
    private function registerViews()
    {
        $viewPath = resource_path('views/vendor/erpnet-profiting-milk');
        
        $sourcePath = $this->projectRootDir . 'resources/views';
        
        $this->publishes([
            $sourcePath => $viewPath
        ]);
        
        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/vendor/erpnet-profiting-milk';
        }, \Config::get('view.paths')), [$sourcePath]), 'erpnet-profiting-milk');
    }
    
    private function mergeConfig()
    {
        //$this->mergeConfigFrom($this->getConfigPath(), 'profiting.category_types');
        
        config([
            'profiting.category_types' => array_merge(
                (require $this->getConfigPath())['category_types'],
                config('profiting.category_types'))
        ]);
    }
        
}

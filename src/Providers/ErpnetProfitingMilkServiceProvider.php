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
        

        
        $this->mergeConfigFrom($this->getConfigPath(), 'profiting');
        dd(config('profiting'));

        //Publish Config
//        $this->publishes([
//            $projectRootDir.'permissions.sh' => base_path('permissions.sh')
//        ], 'erpnetPermissions');

        //Bind Interfaces
//        $app->bind($bindInterface, $bindRepository);
//        foreach (config('erpnetMigrates.tables') as $table => $config) {
//            $routePrefix = isset($config['routePrefix'])?$config['routePrefix']:str_singular($table);
//            $bindInterface = '\\ErpNET\\Models\\v1\\Interfaces\\'.(isset($config['bindInterface'])?$config['bindInterface']:(ucfirst($routePrefix).'Repository'));
//            $bindRepository = '\\ErpNET\\Models\\v1\\Repositories\\'.(isset($config['bindRepository'])?$config['bindRepository']:(ucfirst($routePrefix).'RepositoryEloquent'));
//
//            if(interface_exists($bindInterface)  && class_exists($bindRepository)){
//                $app->bind($bindInterface, $bindRepository);
//            }
//        }

        //Routing
        $routesDir = $this->getProjectRootDir()."routes".DIRECTORY_SEPARATOR;
//        include $routesDir."api.php";
        include $routesDir."web.php";
        
        $this->publishMigrations();
        
        $this->listenEvents();
        
        $this->registerTranslations();
        
        $this->registerViews();

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
        
}

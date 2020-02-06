<?php

namespace ErpNET\Profiting\Milk\Providers;

use Illuminate\Support\ServiceProvider;

class ErpnetProfitingMilkServiceProvider extends ServiceProvider
{
    protected $commands = [
        \ErpNET\Profiting\Milk\Console\Commands\Install::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        $projectRootDir = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR;
        $routesDir = $projectRootDir."routes".DIRECTORY_SEPARATOR;

//        $configPath = $projectRootDir . 'config/erpnetModels.php';
//        $this->mergeConfigFrom($configPath, 'erpnetModels');

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
//        include $routesDir."api.php";
        include $routesDir."web.php";
        
        $this->publishMigrations();

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
    
    private function getMigrationsPath()
    {
        return __DIR__ . '/../database/migrations/';
    }
}

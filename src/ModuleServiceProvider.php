<?php
namespace Llama\Modules;

use Illuminate\Support\ServiceProvider;
use Llama\Modules\Providers\BootstrapServiceProvider;
use Llama\Modules\Providers\ConsoleServiceProvider;
use Llama\Modules\Providers\ContractServiceProvider;
use Llama\Modules\Support\Stub;

class ModuleServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerNamespaces();
        
        $this->registerModules();
    }

    /**
     * Register all modules.
     */
    protected function registerModules()
    {
        $this->app->register(BootstrapServiceProvider::class);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();
        $this->registerStubPath();
        $this->registerProviders();
    }

    /**
     * Register stub path.
     */
    public function registerStubPath()
    {
        $this->app->booted(function ($app) {
            Stub::setBasePath($app['modules']->config('stubs.path') ?: __DIR__ . '/Commands/stubs');
        });
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../config/config.php';
        $this->mergeConfigFrom($configPath, 'llama.modules');
        $this->publishes([
            $configPath => config_path('llama/modules.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    protected function registerServices()
    {
        $this->app->singleton('modules', function ($app) {
            return new Repository($app, $app['config']->get('llama.modules.paths.module'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'modules'
        ];
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(ContractServiceProvider::class);
    }
}

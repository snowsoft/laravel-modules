<?php
namespace Llama\Modules;

use Countable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Llama\Modules\Contracts\RepositoryInterface;
use Llama\Modules\Exceptions\ModuleNotFoundException;
use Llama\Modules\Process\Installer;
use Llama\Modules\Process\Updater;

class Repository implements RepositoryInterface, Countable
{

    /**
     * Application instance.
     *
     * @var Application
     */
    protected $laravel;

    /**
     * The module path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned locations.
     *
     * @var array
     */
    protected $locations = [];

    /**
     * The ordered modules.
     *
     * @var array
     */
    protected $orders = [];

    /**
     * The constructor.
     *
     * @param Application $laravel            
     * @param string|null $path            
     */
    public function __construct(Application $app, $path = null)
    {
        $this->laravel = $app;
        $this->path = $path;
    }

    /**
     * Add other module location.
     *
     * @param string $path            
     *
     * @return $this
     */
    public function addLocation($path)
    {
        $this->locations[] = $path;
        
        return $this;
    }

    /**
     * Get all additional locations.
     *
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Get scanned module paths.
     *
     * @return array
     */
    public function getScannedLocations()
    {
        $locations = $this->locations;
        
        $locations[] = $this->getPath() . '/*';
        
        if ($this->config('scan.enabled')) {
            $locations = array_merge($locations, $this->config('scan.paths'));
        }
        
        return $locations;
    }

    /**
     * Get & scan all modules.
     *
     * @return array
     */
    public function scan()
    {
        $locations = $this->getScannedLocations();
        
        $modules = [];
        
        foreach ($locations as $key => $path) {
            $manifests = $this->getFiles()->glob("{$path}/module.json");
            
            if (! is_array($manifests)) {
                $manifests = [];
            }
            
            foreach ($manifests as $manifest) {
                $name = Json::make($this->laravel, $manifest)->get('name');
                $modules[$name] = new Module($this->laravel, $name, dirname($manifest));
            }
        }
        
        return $modules;
    }

    /**
     * Get all modules.
     *
     * @return array
     */
    public function all()
    {
        if (! $this->config('cache.enabled')) {
            return $this->scan();
        }
        
        return $this->formatCached($this->getCached());
    }

    /**
     * Format the cached data as array of modules.
     *
     * @param array $cached            
     * @return array
     */
    protected function formatCached($cached)
    {
        $modules = [];
        foreach ($cached as $name => $module) {
            $modules[$name] = new Module($this->laravel, $name, $this->getPath() . '/' . $name);
        }
        
        return $modules;
    }

    /**
     * Get cached modules.
     *
     * @return array
     */
    public function getCached()
    {
        return $this->laravel['cache']->remember($this->config('cache.key'), $this->config('cache.lifetime'), function () {
            return $this->toCollection()
                ->toArray();
        });
    }

    /**
     * Get all modules as collection instance.
     *
     * @return Collection
     */
    public function toCollection()
    {
        return new Collection($this->scan());
    }

    /**
     * Get modules by status.
     *
     * @param int $status            
     * @return array
     */
    public function getByStatus($status)
    {
        $modules = [];
        foreach ($this->all() as $name => $module) {
            if ($module->getStatus($status)) {
                $modules[$name] = $module;
            }
        }
        
        return $modules;
    }

    /**
     * Determine whether the given module exist.
     *
     * @param string $name            
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->all());
    }

    /**
     * Get count from all modules.
     *
     * @return int
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * Get all ordered modules.
     *
     * @param string $direction            
     * @return array
     */
    public function getOrdered($direction = 'asc')
    {
        if (count($this->orders) === 0) {
            $this->orders = $this->all();
            
            uasort($this->orders, function (Module $a, Module $b) use ($direction) {
                if ($a->order == $b->order) {
                    return 0;
                }
                
                if ($direction == 'desc') {
                    return $a->order < $b->order ? 1 : - 1;
                }
                
                return $a->order > $b->order ? 1 : - 1;
            });
        }
        
        return $this->orders;
    }

    /**
     * Get a module path.
     *
     * @return string
     */
    public function getPath()
    {
        return rtrim($this->path ?: base_path('modules'), '/');
    }

    /**
     * Register the modules.
     */
    public function register()
    {
        $modules = $this->getOrdered();
        
        foreach ($modules as $module) {
            $module->register();
        }
        
        // Register the Module eloquent factory instance in the container.
        $this->laravel->singleton(EloquentFactory::class, function ($app) use ($modules) {
            $eloquentFactory = new EloquentFactory($app->make(\Faker\Generator::class));
            
            foreach ($modules as $module) {
                $eloquentFactory->load($module->getFactoryPath());
            }
            
            return $eloquentFactory;
        });
    }

    /**
     * Boot the modules.
     */
    public function boot()
    {
        foreach ($this->getOrdered() as $module) {
            $module->boot();
        }
    }

    /**
     * Create a model factory builder for a given class, name, and amount.
     *
     * @param
     *            dynamic class|class,name|class,amount|class,name,amount
     * @return \Illuminate\Database\Eloquent\FactoryBuilder
     */
    public function factory()
    {
        $factory = app(EloquentFactory::class);
        $arguments = func_get_args();
        
        if (isset($arguments[1]) && is_string($arguments[1])) {
            return $factory->of($arguments[0], $arguments[1])->times(isset($arguments[2]) ? $arguments[2] : null);
        }
        if (isset($arguments[1])) {
            return $factory->of($arguments[0])->times($arguments[1]);
        }
        return $factory->of($arguments[0]);
    }

    /**
     * Find a specific module.
     *
     * @param string $name            
     * @return string|null
     */
    public function find($name)
    {
        foreach ($this->all() as $module) {
            if ($module->getLowerName() === strtolower($name)) {
                return $module;
            }
        }
        
        return null;
    }

    /**
     * Alternative for "find" method.
     *
     * @param string $name            
     * @return mixed|void
     */
    public function get($name)
    {
        return $this->find($name);
    }

    /**
     * Find a specific module, if there return that, otherwise throw exception.
     *
     * @param string $name            
     * @return Module
     * @throws ModuleNotFoundException
     */
    public function findOrFail($name)
    {
        $module = $this->find($name);
        if ($module instanceof Module) {
            return $module;
        }
        
        throw new ModuleNotFoundException("Module [{$name}] does not exist!");
    }

    /**
     * Get all modules as laravel collection instance.
     *
     * @return Collection
     */
    public function collections()
    {
        return new Collection($this->all());
    }

    /**
     * Get module path for a specific module.
     *
     * @param string $module            
     * @return string
     */
    public function getModulePath($module)
    {
        try {
            return $this->findOrFail($module)->getPath() . '/';
        } catch (ModuleNotFoundException $e) {
            return $this->getPath() . '/' . Str::studly($module) . '/';
        }
    }

    /**
     * Get asset path for a specific module.
     *
     * @param string $module            
     * @return string
     */
    public function assetPath($module)
    {
        return $this->getAssetsPath() . '/' . $module;
    }

    /**
     * Get a specific config data from a configuration file.
     *
     * @param string $key            
     * @param mixed $default            
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return $this->laravel['config']->get('llama.modules.' . $key, $default);
    }

    /**
     * Get storage path for module used.
     *
     * @return string
     */
    public function getUsedStoragePath()
    {
        if (! $this->getFiles()->exists($path = storage_path('app/modules'))) {
            $this->getFiles()->makeDirectory($path, 0777, true);
        }
        
        return $path . '/modules.used';
    }

    /**
     * Setter and getter used module for cli session.
     *
     * @param string $name            
     * @return string|null
     * @throws ModuleNotFoundException
     */
    public function used($name = null)
    {
        // Throw exeption if you aren't running in the console.
        if (! app()->runningInConsole()) {
            throw new \RuntimeException('Using the terminal command line to run this feature.');
        }
        
        if (is_null($name)) {
            return $this->findOrFail($this->getFiles()
                ->get($this->getUsedStoragePath()))
                ->getStudlyName();
        }
        
        $this->getFiles()->put($this->getUsedStoragePath(), $this->findOrFail($name)
            ->getStudlyName());
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles()
    {
        return $this->laravel['files'];
    }

    /**
     * Get module assets path.
     *
     * @return string
     */
    public function getAssetsPath()
    {
        return $this->config('paths.asset', public_path('modules'));
    }

    /**
     * Get asset url from a specific module.
     *
     * @param string $asset            
     * @return string
     */
    public function asset($asset)
    {
        list ($name, $url) = explode('::', $asset, 2);
        
        return str_replace([
            'http://',
            'https://'
        ], '//', $this->laravel['url']->asset(str_replace(public_path() . DIRECTORY_SEPARATOR, '', $this->getAssetsPath()) . "/{$name}/" . $url));
    }

    /**
     * Determine whether the given module is activated.
     *
     * @return bool
     */
    public function activated()
    {
        return $this->getByStatus(1);
    }

    /**
     * Determine whether the given module is not activated.
     *
     * @return bool
     */
    public function deactivated()
    {
        return $this->getByStatus(0);
    }

    /**
     * Enabling a specific module.
     *
     * @param string $name            
     * @return bool
     */
    public function enable($name)
    {
        return $this->findOrFail($name)->enable();
    }

    /**
     * Disabling a specific module.
     *
     * @param string $name            
     * @return bool
     */
    public function disable($name)
    {
        return $this->findOrFail($name)->disable();
    }

    /**
     * Delete a specific module.
     *
     * @param string $name            
     * @return bool
     */
    public function delete($name)
    {
        return $this->findOrFail($name)->delete();
    }

    /**
     * Update dependencies for the specified module.
     *
     * @param string $module            
     */
    public function update($module)
    {
        with(new Updater($this))->update($module);
    }

    /**
     * Install the specified module.
     *
     * @param string $name            
     * @param string $version            
     * @param string $type            
     * @param bool $subtree            
     * @return \Symfony\Component\Process\Process
     */
    public function install($name, $version = 'dev-master', $type = 'composer', $subtree = false)
    {
        return with(new Installer($name, $version, $type, $subtree))->run();
    }

    /**
     * Get module namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->config('namespace');
    }
}

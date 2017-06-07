<?php
namespace Llama\Modules;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class Module extends ServiceProvider
{

    /**
     * The laravel application instance.
     *
     * @var Application
     */
    protected $laravel;

    /**
     * The module name.
     *
     * @var string
     *
     */
    protected $name;

    /**
     * The module path,.
     *
     * @var string
     */
    protected $path;

    /**
     *
     * @var array
     */
    protected $settings = [];

    /**
     * The constructor.
     *
     * @param Application $app            
     * @param
     *            $name
     * @param
     *            $path
     */
    public function __construct(Application $app, $name, $path)
    {
        $this->laravel = $app;
        $this->name = $name;
        $this->path = realpath($path);
    }

    /**
     * Get laravel instance.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function getLaravel()
    {
        return $this->laravel;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name in lower case.
     *
     * @return string
     */
    public function getLowerName()
    {
        return strtolower($this->name);
    }

    /**
     * Get name in studly case.
     *
     * @return string
     */
    public function getStudlyName()
    {
        return Str::studly($this->name);
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param string $path            
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        
        return $this;
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->fireEvent('boot');
    }

    /**
     * Get module.json contents.
     *
     * @return Json
     */
    public function json()
    {
        if (! isset($this->settings['module'])) {
            $this->settings['module'] = new Json($this->laravel, $this->getPath() . '/module.json', $this->laravel['files']);
        }
        
        return $this->settings['module'];
    }

    /**
     * Get composer.json contents.
     *
     * @return Json
     */
    public function composer()
    {
        if (! isset($this->settings['composer'])) {
            $this->settings['composer'] = new Json($this->laravel, $this->getPath() . '/composer.json', $this->laravel['files']);
        }
        
        return $this->settings['composer'];
    }

    /**
     * Register the module.
     */
    public function register()
    {
        $this->registerAliases();
        $this->registerProviders();
        $this->registerFiles();
        
        $this->fireEvent('register');
    }

    /**
     * Register the module event.
     *
     * @param string $event            
     */
    protected function fireEvent($event)
    {
        $this->laravel['events']->fire(sprintf('modules.%s.' . $event, $this->getLowerName()), [
            $this
        ]);
    }

    /**
     * Register the aliases from this module.
     */
    protected function registerAliases()
    {
        $loader = AliasLoader::getInstance();
        foreach ($this->json()->get('aliases', []) as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }

    /**
     * Register the service providers from this module.
     */
    protected function registerProviders()
    {
        foreach ($this->json()->get('providers', []) as $provider) {
            $this->laravel->register($provider);
        }
    }

    /**
     * Register the files from this module.
     */
    protected function registerFiles()
    {
        foreach ($this->json()->get('autoload.files', []) as $file) {
            $this->laravel['files']->requireOnce($this->path . '/' . $file);
        }
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getStudlyName();
    }

    /**
     * Determine whether the given status same with the current module status.
     *
     * @param
     *            $status
     *            
     * @return bool
     */
    public function getStatus($status)
    {
        return $this->json()->get('active', 0) === $status;
    }

    /**
     * Set active state for current module.
     *
     * @param
     *            $active
     *            
     * @return bool
     */
    public function setStatus($status)
    {
        return $this->json()
            ->set('active', $status)
            ->save();
    }

    /**
     * Determine whether the current module activated.
     *
     * @return bool
     */
    public function activated()
    {
        return $this->getStatus(1);
    }

    /**
     * Determine whether the current module not activated.
     *
     * @return bool
     */
    public function deactivated()
    {
        return ! $this->activated();
    }

    /**
     * Disable the current module.
     *
     * @return bool
     */
    public function disable()
    {
        $this->laravel['events']->fire('module.disabling', [
            $this
        ]);
        
        $this->setStatus(0);
        
        $this->laravel['events']->fire('module.disabled', [
            $this
        ]);
    }

    /**
     * Enable the current module.
     */
    public function enable()
    {
        $this->laravel['events']->fire('module.enabling', [
            $this
        ]);
        
        $this->setStatus(1);
        
        $this->laravel['events']->fire('module.enabled', [
            $this
        ]);
    }

    /**
     * Delete the current module.
     *
     * @return bool
     */
    public function delete()
    {
        return $this->json()
            ->getFilesystem()
            ->deleteDirectory($this->getPath());
    }

    /**
     * Get extra path.
     *
     * @param string $path            
     * @return string
     */
    public function getExtraPath($path)
    {
        return $this->getPath() . '/' . $path;
    }

    /**
     * Handle call to __get method.
     *
     * @param string $key            
     * @return mixed
     */
    public function __get($key)
    {
        return $this->json()->get($key);
    }

    /**
     * Get migration path.
     *
     * @return string
     */
    public function getMigrationPath()
    {
        return $this->getExtraPath($this->laravel['modules']->config('paths.generator.migration', 'Database/Migrations'));
    }

    /**
     * Get model factory path.
     *
     * @return string
     */
    public function getFactoryPath()
    {
        return $this->getExtraPath($this->laravel['modules']->config('paths.generator.factory', 'Database/Factories'));
    }

    /**
     * Get module namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->laravel['modules']->getNamespace() . '\\' . $this->getStudlyName();
    }
}

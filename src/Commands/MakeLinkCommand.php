<?php
namespace Llama\Modules\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Llama\Modules\Module;

class MakeLinkCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link';

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($name = $this->argument('module')) {
            return $this->publish($this->laravel['modules']->findOrFail($name));
        }
        
        foreach ($this->laravel['modules']->activated() as $module) {
            $this->publish($module);
        }
    }

    /**
     * Publish assets from the specified module.
     *
     * @param Module $module
     */
    protected function publish(Module $module)
    {
        if (file_exists($this->getPublicAssetPath($module))) {
            return $this->error('The "' . $this->getPublicAssetPath($module) . '" directory already exists.');
        }
        
        $this->laravel->make('files')->link($this->getAssetPath($module), $this->getPublicAssetPath($module));
        
        $this->info('The [' . $this->getPublicAssetPath($module) . '] directory has been linked.');
    }

    /**
     * Get the destination module's asset path.
     *
     * @param Module $module
     * @return string
     */
    protected function getAssetPath(Module $module)
    {
        return $this->laravel['modules']->getPath() . DIRECTORY_SEPARATOR . $module->getStudlyName() . DIRECTORY_SEPARATOR . $this->laravel['modules']->config('paths.generator.asset', 'Resources/assets');
    }

    /**
     * Get the destination public asset path.
     *
     * @param Module $module
     * @return string
     */
    protected function getPublicAssetPath(Module $module)
    {
        return $this->laravel['modules']->config('paths.asset', public_path('modules')) . DIRECTORY_SEPARATOR . strtolower($module->getStudlyName());
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'module',
                InputArgument::OPTIONAL,
                'The name of module will be linked.'
            ]
        ];
    }
}

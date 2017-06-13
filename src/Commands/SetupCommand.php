<?php
namespace Llama\Modules\Commands;

use Illuminate\Console\Command;
use Llama\Modules\Json;

class SetupCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting up modules folder for first use.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->generateModulesFolder();
        $this->generateAssetsFolder();
        $this->generateComposerAutoloadingSections();
    }

    /**
     * Generate the modules folder.
     */
    protected function generateModulesFolder()
    {
        $this->generateDirectory($this->laravel['modules']->getPath(), 'Modules directory created successfully', 'Modules directory already exist');
    }

    /**
     * Generate the assets folder.
     */
    protected function generateAssetsFolder()
    {
        $this->generateDirectory($this->laravel['modules']->getAssetsPath(), 'Assets directory created successfully', 'Assets directory already exist');
    }

    /**
     * Generate the specified directory by given $dir.
     *
     * @param string $dir
     * @param string $success
     * @param string $error
     */
    protected function generateDirectory($dir, $success, $error)
    {
        if (! $this->laravel['files']->isDirectory($dir)) {
            $this->laravel['files']->makeDirectory($dir);
            return $this->info($success);
        }
        
        $this->error($error);
    }

    /**
     * Adding processing autoloading sections to the command line interface.
     */
    protected function generateComposerAutoloadingSections()
    {
        try {
            // Modify composer.json
            $composerJson = Json::make($this->laravel, base_path('composer.json'));
            $composerJson->add('autoload.psr-4.' . $this->laravel['modules']->getNamespace() . '\\', trim(str_replace(base_path(), '', $this->laravel['modules']->config('paths.module')), '/') . '/');
            $composerJson->save();
            
            // Regenerate the optimized Composer autoloader files.
            $this->laravel['composer']->dumpOptimized();
            
            $this->info('The module namespace added successfully');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}

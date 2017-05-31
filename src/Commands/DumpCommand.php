<?php
namespace Llama\Modules\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Command;

class DumpCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:dump-autoload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump-autoload the specified module or for all module.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->info('Generating optimized autoload modules.');
        
        if ($module = $this->argument('module')) {
            $this->dump($module);
        } else {
            foreach ($this->laravel['modules']->getOrdered() as $module) {
                $this->dump($module->getStudlyName());
            }
        }
    }

    public function dump($module)
    {
        $module = $this->laravel['modules']->findOrFail($module);
        
        $this->line("<comment>Running for module</comment>: {$module}");
        
        chdir($module->getPath());
        
        passthru('composer dump-autoload --apcu');
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
                'Module name.'
            ]
        ];
    }
}

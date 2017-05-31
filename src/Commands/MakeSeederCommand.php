<?php
namespace Llama\Modules\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Llama\Modules\Traits\ModuleCommandTrait;

class MakeSeederCommand extends SeederMakeCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new seeder for the specified module.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of seeder will be created.'
            ],
            [
                'module',
                InputArgument::OPTIONAL,
                'The name of module will be used.'
            ]
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'master',
                null,
                InputOption::VALUE_NONE,
                'Indicates the seeder will created is a master database seeder.'
            ]
        ];
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name            
     * @return string
     */
    protected function qualifyClass($name)
    {
        if (Str::endsWith(strtolower($name), 'seeder') === false) {
            $name .= ($this->option('master') ? 'DatabaseSeeder' : 'TableSeeder');
        }
        
        return Str::studly($name);
    }

    /**
     * Get the destination class path.
     *
     * @param string $name            
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel['modules']->getPath() . '/' . $this->getModuleName() . '/' . $this->laravel['modules']->config('paths.generator.seed', 'Database/Seeds') . '/' . $name . '.php';
    }
}

<?php
namespace Llama\Modules\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;
use Llama\Modules\Generators\ModuleGenerator;

class MakeModuleCommand extends Command
{
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
    protected $name = 'module:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new module.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $names = $this->argument('name');
        
        foreach ($names as $name) {
            with(new ModuleGenerator($name))->setFilesystem($this->laravel['files'])
                ->setModule($this->laravel['modules'])
                ->setConsole($this)
                ->setForce($this->option('force'))
                ->setPlain($this->option('plain'))
                ->generate();
        }
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
                'name',
                InputArgument::IS_ARRAY,
                'The names of modules will be created.'
            ]
        ];
    }

    protected function getOptions()
    {
        return [
            [
                'plain',
                'p',
                InputOption::VALUE_NONE,
                'Generate a plain module (without some resources).'
            ],
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when module already exist.'
            ]
        ];
    }
}

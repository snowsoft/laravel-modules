<?php
namespace Llama\Modules\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Llama\Modules\Traits\ModuleCommandTrait;
use Llama\Modules\Support\Stub;

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
        $rootNamespace = $this->rootNamespace();
        
        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }
        
        $name = str_replace('/', '\\', $name);
        
        return $this->qualifyClass($this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name);
    }

    /**
     *
     * @return array|string
     */
    protected function getNameInput()
    {
        $name = Str::studly(parent::getNameInput());
        if (Str::contains(strtolower($name), 'seeder') === false) {
            $name .= ($this->option('master') ? 'DatabaseSeeder' : 'TableSeeder');
        }
        
        return $name;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return with(new Stub('/seeder.stub'))->getPath();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace            
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Database\Seeds';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel['modules']->getNamespace() . '\\' . $this->getModuleName();
    }

    /**
     * Get the destination class path.
     *
     * @param string $name            
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel['modules']->getPath() . '/' . $this->getModuleName() . str_replace('\\', '/', str_replace_first($this->rootNamespace(), '', $name)) . '.php';
    }
}

<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class MakeModelCommand extends ModelMakeCommand
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
    protected $name = 'module:make-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new model for the specified module.';

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
                'The name of model will be created.'
            ],
            [
                'module',
                InputArgument::OPTIONAL,
                'The name of module will be used.'
            ]
        ];
    }
    
    /**
     *
     * @return array|string
     */
    protected function getNameInput()
    {
        $name = Str::studly(parent::getNameInput());
        if (Str::contains(strtolower($name), 'model') === false) {
            $name .= 'Model';
        }
        
        return $name;
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
    
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Models';
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));
        $this->call('module:make-migration', [
            'name' => "create_{$table}_table",
            'module' => $this->getModuleName()
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $this->call('module:make-controller', [
            'controller' => Str::studly(class_basename($this->argument('name'))),
            'module' => $this->getModuleName()
        ]);
    }
}

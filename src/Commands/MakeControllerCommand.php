<?php
namespace Llama\Modules\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Llama\Modules\Traits\ModuleCommandTrait;
use Llama\Modules\Support\Stub;
use Llama\Modules\Traits\ReplaceNamespaceTrait;

class MakeControllerCommand extends ControllerMakeCommand
{
    use ModuleCommandTrait;
    use ReplaceNamespaceTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new restful controller for the specified module.';

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
                'The name of the controller class.'
            ],
            [
                'module',
                InputArgument::OPTIONAL,
                'The name of module will be used.'
            ]
        ];
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = Str::studly(parent::getNameInput());
        if (Str::contains(strtolower($name), 'controller') === false) {
            $name .= 'Controller';
        }
        
        return $name;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param string $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return $this->getDefaultNamespace($this->laravel['modules']->getNamespace() . '\\' . $this->getModuleName() . trim(implode('\\', array_slice(explode('\\', $name), 0, - 1)), '\\'));
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        return str_replace('\\', '/', $this->getDefaultNamespace($this->laravel['modules']->getPath() . '/' . $this->getModuleName()) . '/' . str_replace_first($this->rootNamespace(), '', $name)) . '.php';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return $name;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stubPath = '/controller.stub';
        if (! $this->option('resource')) {
            $stubPath = '/plain/controller.stub';
        }
        
        return (new Stub($stubPath))->getPath();
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [
            'DummyViewNamespace' => strtolower($this->getModuleName())
        ];
        
        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }
}

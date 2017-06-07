<?php
namespace Llama\Modules\Commands;

use Illuminate\Support\Str;
use Llama\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Foundation\Console\PolicyMakeCommand;
use Llama\Modules\Support\Stub;

class MakePolicyCommand extends PolicyMakeCommand
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
    protected $name = 'module:make-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new policy for the specified module';

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
                'The name of the event.'
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
        if (Str::contains(strtolower($name), 'policy') === false) {
            $name .= 'Policy';
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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stubPath = '/plain/policy.stub';
        if ($this->option('model')) {
            $stubPath = '/policy.stub';
        }
        
        return with(new Stub($stubPath))->getPath();
    }

    /**
     * Replace the model for the given stub.
     *
     * @param string $stub            
     * @param string $model            
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $model = str_replace('/', '\\', $model);
        if (! Str::startsWith($model, '\\')) {
            $model = '\\' . $this->rootNamespace() . '\\Models\\' . $model;
        }
        
        return parent::replaceModel($stub, $model);
    }
}

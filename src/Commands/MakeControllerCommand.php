<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Support\Stub;
use Llama\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeControllerCommand extends BaseCommand
{
    use ModuleCommandTrait;

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
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        return $this->laravel['modules']->getModulePath($this->getModuleName()) . $this->getDefaultNamespace() . '/' . $this->getControllerName() . '.php';
    }

    /**
     *
     * @return string
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());
        
        return with(new Stub($this->getStubName(), [
            'MODULENAME' => $module->getStudlyName(),
            'CONTROLLERNAME' => $this->getControllerName(),
            'NAMESPACE' => $module->getStudlyName(),
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getControllerName(),
            'LOWER_NAME' => $module->getLowerName(),
            'MODULE' => $this->getModuleName(),
            'NAME' => $this->getModuleName(),
            'STUDLY_NAME' => $module->getStudlyName(),
            'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace')
        ]))->render();
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
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'plain',
                'p',
                InputOption::VALUE_NONE,
                'Generate a plain controller',
                null
            ]
        ];
    }

    /**
     *
     * @return array|string
     */
    protected function getControllerName()
    {
        $controller = studly_case($this->argument('controller'));
        if (str_contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }
        
        return $controller;
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace()
    {
        return $this->laravel['modules']->config('paths.generator.controller', 'Http/Controllers');
    }

    /**
     * Get the stub file name based on the plain option
     *
     * @return string
     */
    private function getStubName()
    {
        if ($this->option('plain') === true) {
            return '/plain/controller.stub';
        }
        
        return '/controller.stub';
    }
}

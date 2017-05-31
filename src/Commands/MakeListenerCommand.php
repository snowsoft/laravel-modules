<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Foundation\Console\ListenerMakeCommand;

class MakeListenerCommand extends ListenerMakeCommand
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
    protected $name = 'module:make-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new listener for the specified module';

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
                'The name of the command.'
            ],
            [
                'module',
                InputArgument::OPTIONAL,
                'The name of module will be used.'
            ]
        ];
    }
    
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel['modules']->config('namespace') . '\\' . $this->getModuleName();
    }

    /**
     * Get the destination class path.
     *
     * @param string $name            
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel['modules']->getPath() . '/' . $this->getModuleName() . '/' . str_replace('\\', '/', str_replace_first($this->rootNamespace(), '', $name)) . '.php';
    }
}

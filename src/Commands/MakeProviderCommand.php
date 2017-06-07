<?php
namespace Llama\Modules\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Foundation\Console\ProviderMakeCommand;
use Llama\Modules\Support\Stub;
use Llama\Modules\Traits\ModuleCommandTrait;
use Llama\Modules\Traits\ReplaceNamespaceTrait;

class MakeProviderCommand extends ProviderMakeCommand
{
    use ModuleCommandTrait;
    use ReplaceNamespaceTrait;

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
    protected $name = 'module:make-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new service provider for the specified module.';

    /**
     * The command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The master service provider name.'
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
                'plain',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Indicates a plain master service provider',
                true
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
        if (Str::contains(strtolower($name), 'provider') === false) {
            $name .= 'Provider';
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
        if ($this->option('plain') === true) {
            return parent::getStub();
        }
        
        return with(new Stub('/module-provider.stub'))->getPath();
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

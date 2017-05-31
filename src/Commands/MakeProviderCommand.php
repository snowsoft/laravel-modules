<?php
namespace Llama\Modules\Commands;

use Illuminate\Support\Str;
use Llama\Modules\Support\Stub;
use Llama\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeProviderCommand extends BaseCommand
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
    protected $name = 'module:make-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new service provider for the specified module.';
    
    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace()
    {
        return $this->laravel['modules']->config('paths.generator.provider', 'Providers');
    }

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
                InputOption::VALUE_NONE,
                'Indicates a plain master service provider',
                null
            ]
        ];
    }

    /**
     *
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());
        
        return with(new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getClass(),
            'LOWER_NAME' => $module->getLowerName(),
            'MODULE' => $this->getModuleName(),
            'NAME' => $this->getFileName(),
            'STUDLY_NAME' => $module->getStudlyName(),
            'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
            'PATH_VIEW' => $this->laravel['modules']->config('paths.generator.view'),
            'PATH_LANG' => $this->laravel['modules']->config('paths.generator.lang'),
            'PATH_CONFIG' => $this->laravel['modules']->config('paths.generator.config')
        ]))->render();
    }

    /**
     *
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        return $this->laravel['modules']->getModulePath($this->getModuleName()) . $this->getDefaultNamespace() . '/' . $this->getFileName() . '.php';
    }

    /**
     *
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
    
    /**
     * Get the stub file name based on the plain option
     *
     * @return string
     */
    private function getStubName()
    {
        if ($this->option('plain') === true) {
            return '/plain/module-provider.stub';
        }
        
        return '/module-provide.stub';
    }
}

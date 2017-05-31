<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Support\Stub;
use Llama\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeRouteProviderCommand extends BaseCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'module';

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'module:make-route';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Generate a new route service provider for the specified module.';

    /**
     * The command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
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
                'Indicates a plain route service provider',
                null
            ]
        ];
    }

    /**
     * Get template contents.
     *
     * @return string
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
            'PATH_ROUTE' => $this->laravel['modules']->config('paths.generator.route')
        ]))->render();
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        return $this->laravel['modules']->getModulePath($this->getModuleName()) . $this->getDefaultNamespace() . '/' . $this->getFileName() . '.php';
    }

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
     *
     * @return string
     */
    private function getFileName()
    {
        return $this->getModuleName() . 'RouteServiceProvider';
    }

    /**
     * Get the stub file name based on the plain option
     *
     * @return string
     */
    private function getStubName()
    {
        if ($this->option('plain') === true) {
            return '/plain/route-provider.stub';
        }
        
        return '/route-provide.stub';
    }
}

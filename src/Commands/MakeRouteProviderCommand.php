<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Support\Stub;
use Llama\Modules\Traits\ModuleCommandTrait;

class MakeRouteProviderCommand extends MakeProviderCommand
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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stubPath = '/route-provider.stub';
        if ($this->option('plain') === true) {
            $stubPath = '/plain/route-provider.stub';
        }
        
        return with(new Stub($stubPath))->getPath();
    }

    /**
     * Replace the route path for the given stub.
     *
     * @return string
     */
    protected function replaceRoutePath()
    {
        return $this->laravel['modules']->config('paths.generator.route');
    }

    /**
     * Replace the controller namespace for the given stub.
     *
     * @return string
     */
    protected function replaceControllerNamespace()
    {
        return $this->rootNamespace() . '\\' . $this->getModuleName() . '\\Http\\Controllers';
    }

    /**
     * Get the list for the replacements.
     *
     * @return array
     */
    protected function getReplacements()
    {
        return array_merge(parent::getReplacements(), [
            'DummyControllerNamespace' => [
                $this,
                'replaceControllerNamespace'
            ],
            'DummyRoutePath' => [
                $this,
                'replaceRoutePath'
            ]
        ]);
    }
}

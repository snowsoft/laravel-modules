<?php
namespace Llama\Modules\Traits;

trait ReplaceNamespaceTrait
{

    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub            
     * @param string $name            
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        parent::replaceNamespace($stub, $name);
        
        foreach ($this->getReplacements() as $search => $callback) {
            $stub = str_replace($search, call_user_func_array($callback, [
                $this,
                $stub,
                $name
            ]), $stub);
        }
        
        return $this;
    }

    /**
     * Replace the module lower name for the given stub.
     *
     * @return string
     */
    protected function replaceModuleLowerName()
    {
        return strtolower($this->getModuleName());
    }

    /**
     * Replace the config path for the given stub.
     *
     * @return string
     */
    protected function replaceConfigPath()
    {
        return $this->laravel['modules']->config('paths.generator.config');
    }

    /**
     * Replace the view path for the given stub.
     *
     * @return string
     */
    protected function replaceViewPath()
    {
        return $this->laravel['modules']->config('paths.generator.view');
    }

    /**
     * Replace the language path for the given stub.
     *
     * @return string
     */
    protected function replaceLanguagePath()
    {
        return $this->laravel['modules']->config('paths.generator.lang');
    }

    /**
     * Get the list for the replacements.
     *
     * @return array
     */
    protected function getReplacements()
    {
        return [
            'DummyLowerName' => [
                $this,
                'replaceModuleLowerName'
            ],
            'DummyConfigPath' => [
                $this,
                'replaceConfigPath'
            ],
            'DummyViewPath' => [
                $this,
                'replaceViewPath'
            ],
            'DummyLanguagePath' => [
                $this,
                'replaceLanguagePath'
            ]
        ];
    }
}

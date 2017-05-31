<?php
namespace Llama\Modules\Traits;

trait ModuleCommandTrait
{

    /**
     * Get the module name.
     *
     * @return string
     */
    public function getModuleName()
    {
        $module = $this->argument('module') ?: $this->laravel['modules']->used();
        
        return $this->laravel['modules']->findOrFail($module)->getStudlyName();
    }
}

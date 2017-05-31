<?php
namespace Llama\Modules\Traits;

trait CanClearModulesCache
{

    /**
     * Clear the modules cache if it is enabled
     */
    public function clearCache()
    {
        if ($this->laravel['modules']->config('cache.enabled') === true) {
            app('cache')->forget($this->laravel['modules']->config('cache.key'));
        }
    }
}

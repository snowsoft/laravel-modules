<?php
namespace Llama\Modules\Process;

class Updater extends Runner
{

    /**
     * Update the dependencies for the specified module by given the module name.
     *
     * @param string $module            
     */
    public function update($module)
    {
        $module = $this->module->findOrFail($module);
        $packages = $module->composer()->get('require', []);
        
        // Change to root path
        chdir(base_path());
        
        foreach ($packages as $name => $version) {
            $this->run("composer require \"{$name}:{$version}\"");
        }
    }
}

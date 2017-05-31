<?php

namespace Llama\Modules\Commands;

use Llama\Modules\Module;
use Llama\Modules\Publishing\AssetPublisher;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Command;

class PublishAssetCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:publish-asset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a module\'s assets to the application';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if ($name = $this->argument('module')) {
            return $this->publish($this->laravel['modules']->findOrFail($name));
        }

        foreach ($this->laravel['modules']->activated() as $module) {
            $this->publish($module);
        }
    }

    /**
     * Publish assets from the specified module.
     *
     * @param Module $module
     */
    public function publish(Module $module)
    {
        with(new AssetPublisher($module))
            ->setRepository($this->laravel['modules'])
            ->setConsole($this)
            ->publish();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }
}

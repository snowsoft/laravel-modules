<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Traits\MigrationLoaderTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;
use Llama\Modules\Module;

class MigrateResetCommand extends Command
{
    use MigrationLoaderTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the module migrations.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $module = $this->argument('module');
        
        if (! empty($module)) {
            $this->reset($this->laravel['modules']->findOrFail($module));
            
            return;
        }
        
        foreach ($this->laravel['modules']->getOrdered('desc') as $module) {
            $this->line('Reset migration for module: <info>' . $module->getName() . '</info>');
            
            $this->reset($module);
        }
    }

    /**
     * Rollback migration from the specified module.
     *
     * @param Module $module            
     */
    protected function reset(Module $module)
    {
        $this->call('migrate:reset', [
            '--database' => $this->option('database'),
            '--path' => str_replace(base_path(), '', $module->getMigrationPath()),
            '--force' => $this->option('force'),
            '--pretend' => $this->option('pretend')
        ]);
    }

    /**
     * Get the console command arguments.
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
                'database',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database connection to use.'
            ],
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when in production.'
            ],
            [
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run.'
            ]
        ];
    }
}

<?php
namespace Llama\Modules\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;
use Llama\Modules\Module;

class MigrateRollbackCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate-rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the module migrations.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $module = $this->argument('module');
        
        if (! empty($module)) {
            return $this->rollback($this->laravel['modules']->findOrFail($module));
        }
        
        foreach ($this->laravel['modules']->getOrdered() as $module) {
            $this->line('Running for module: <info>' . $module->getName() . '</info>');
            
            $this->rollback($module);
        }
    }

    /**
     * Rollback migration from the specified module.
     *
     * @param Module $module            
     */
    public function rollback(Module $module)
    {
        $this->call('migrate:rollback', [
            '--database' => $this->option('database'),
            '--path' => str_replace(base_path(), '', $module->getMigrationPath()),
            '--step' => $this->option('step'),
            '--pretend' => $this->option('pretend'),
            '--force' => $this->option('force')
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
            ],
            
            [
                'step',
                null,
                InputOption::VALUE_OPTIONAL,
                'The number of migrations to be reverted.'
            ]
        ];
    }
}

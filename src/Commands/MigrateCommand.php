<?php
namespace Llama\Modules\Commands;

use Llama\Modules\Module;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;

class MigrateCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate the migrations from the specified module or from all modules.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if ($name = $this->argument('module')) {
            $module = $this->laravel['modules']->findOrFail($name);
            $this->migrate($module);
            $this->dbSeed($module);
        } else {
            $modules = $this->laravel['modules']->getOrdered();
            foreach ($modules as $module) {
                $this->migrate($module);
            }
            foreach ($modules as $module) {
                $this->dbSeed($module);
            }
        }
    }

    /**
     *
     * @param Module $module            
     */
    protected function migrate(Module $module)
    {
        $this->call('migrate', [
            '--path' => str_replace(base_path(), '', $module->getMigrationPath()),
            '--database' => $this->option('database'),
            '--pretend' => $this->option('pretend'),
            '--force' => $this->option('force')
        ]);
    }

    /**
     *
     * @param Module $module            
     */
    protected function dbSeed(Module $module)
    {
        if ($this->option('seed')) {
            $this->call('module:db-seed', [
                'module' => $module->getName()
            ]);
        }
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
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run.'
            ],
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when in production.'
            ],
            [
                'seed',
                null,
                InputOption::VALUE_NONE,
                'Indicates if the seed task should be re-run.'
            ]
        ];
    }
}

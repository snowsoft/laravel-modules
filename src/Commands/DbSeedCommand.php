<?php
namespace Llama\Modules\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;
use Llama\Modules\Module;
use Llama\Modules\Repository;
use Llama\Modules\Traits\ModuleCommandTrait;
use Llama\Modules\Exceptions\ModuleNotFoundException;

class DbSeedCommand extends Command
{
    use ModuleCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:db-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database seeder from the specified module or from all modules.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        try {
            if ($name = $this->argument('module')) {
                $this->moduleSeeding($this->laravel['modules']->findOrFail($name));
            } else {
                $modules = $this->getModuleRepository()->getOrdered();
                array_walk($modules, [
                    $this,
                    'moduleSeeding'
                ]);
                
                $this->info('All modules seeded.');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     *
     * @return Repository
     * @throws ModuleNotFoundException
     */
    protected function getModuleRepository()
    {
        $modules = $this->laravel['modules'];
        if (! $modules instanceof Repository) {
            throw new ModuleNotFoundException("Module repository not found!");
        }
        return $modules;
    }

    /**
     *
     * @param Module $module            
     * @return void
     */
    protected function moduleSeeding(Module $module)
    {
        if (class_exists($className = $this->getSeederName($module))) {
            $this->dbSeed($className);
            
            $this->info("Module [{$module->getStudlyName()}] seeded.");
        }
    }

    /**
     * Get master database seeder name for the specified module.
     *
     * @param Module $module            
     * @return string
     */
    protected function getSeederName(Module $module)
    {
        return $module->getNamespace() . '\\Database\\Seeds\\' . ($this->option('class') ?: $module->getStudlyName() . 'DatabaseSeeder');
    }

    /**
     * Seed the specified module.
     *
     * @param string $className            
     *
     * @return array
     */
    protected function dbSeed($className)
    {
        $params = [
            '--class' => $className
        ];
        
        if ($option = $this->option('database')) {
            $params['--database'] = $option;
        }
        
        if ($option = $this->option('force')) {
            $params['--force'] = $option;
        }
        
        $this->call('db:seed', $params);
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
                'class',
                null,
                InputOption::VALUE_OPTIONAL,
                'The class name of the root seeder'
            ],
            [
                'database',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database connection to seed.'
            ],
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the operation to run when in production.'
            ]
        ];
    }
}

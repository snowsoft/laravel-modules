<?php
namespace Llama\Modules\Commands;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;

class ListCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of all modules.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->table([
            'Name',
            'Status',
            'Order',
            'Path'
        ], $this->getRows());
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function getRows()
    {
        $rows = [];
        foreach ($this->getModules() as $module) {
            $rows[] = [
                $module->getStudlyName(),
                $module->activated() ? 'Enabled' : 'Disabled',
                $module->json()->get('order'),
                $module->getPath()
            ];
        }
        
        return $rows;
    }

    public function getModules()
    {
        switch ($this->option('only')) {
            case 'enabled':
                return $this->laravel['modules']->getByStatus(1);
            case 'disabled':
                return $this->laravel['modules']->getByStatus(0);
            case 'ordered':
                return $this->laravel['modules']->getOrdered($this->option('direction'));
            default:
                return $this->laravel['modules']->all();
        }
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
                'only',
                null,
                InputOption::VALUE_OPTIONAL,
                'Types of modules will be displayed.',
                null
            ],
            [
                'direction',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The direction of ordering.',
                'asc'
            ]
        ];
    }
}

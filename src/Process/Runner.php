<?php
namespace Llama\Modules\Process;

use Llama\Modules\Contracts\RunableInterface;
use Llama\Modules\Repository;

class Runner implements RunableInterface
{

    /**
     * The module instance.
     *
     * @var \Llama\Modules\Repository
     */
    protected $module;

    /**
     * The constructor.
     *
     * @param \Llama\Modules\Repository $module            
     */
    public function __construct(Repository $module)
    {
        $this->module = $module;
    }

    /**
     * Run the given command.
     *
     * @param string $command            
     */
    public function run($command)
    {
        passthru($command);
    }
}

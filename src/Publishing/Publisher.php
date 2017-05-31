<?php
namespace Llama\Modules\Publishing;

use Illuminate\Console\Command;
use Llama\Modules\Contracts\PublisherInterface;
use Llama\Modules\Module;
use Llama\Modules\Repository;

abstract class Publisher implements PublisherInterface
{

    /**
     * The name of module will used.
     *
     * @var string
     */
    protected $module;

    /**
     * The modules repository instance.
     *
     * @var \Llama\Modules\Repository
     */
    protected $repository;

    /**
     * The laravel console instance.
     *
     * @var Command
     */
    protected $console;

    /**
     * The success message will displayed at console.
     *
     * @var string
     */
    protected $success;

    /**
     * The error message will displayed at console.
     *
     * @var string
     */
    protected $error = '';

    /**
     * Determine whether the result message will shown in the console.
     *
     * @var bool
     */
    protected $showMessage = true;

    /**
     * Laravel Application instance.
     *
     * @var \Illuminate\Foundation\Application.
     */
    protected $laravel;

    /**
     * The constructor.
     *
     * @param Module $module            
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->laravel = $module->getLaravel();
    }

    /**
     * Show the result message.
     *
     * @return self
     */
    public function showMessage()
    {
        $this->showMessage = true;
        
        return $this;
    }

    /**
     * Hide the result message.
     *
     * @return self
     */
    public function hideMessage()
    {
        $this->showMessage = false;
        
        return $this;
    }

    /**
     * Get module instance.
     *
     * @return \Llama\Modules\Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set modules repository instance.
     *
     * @param \Llama\Modules\Repository $repository            
     *
     * @return $this
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
        
        return $this;
    }

    /**
     * Get modules repository instance.
     *
     * @return \Llama\Modules\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set console instance.
     *
     * @param Command $console            
     *
     * @return $this
     */
    public function setConsole(Command $console)
    {
        $this->console = $console;
        
        return $this;
    }

    /**
     * Get console instance.
     *
     * @return Command
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->repository->getFiles();
    }

    /**
     * Get destination path.
     *
     * @return string
     */
    abstract public function getDestinationPath();

    /**
     * Get source path.
     *
     * @return string
     */
    abstract public function getSourcePath();

    /**
     * Publish something.
     */
    public function publish()
    {
        if (! $this->console instanceof Command) {
            throw new \RuntimeException("The 'console' property must instance of \\Illuminate\\Console\\Command.");
        }
        
        if (! $this->getFilesystem()->isDirectory($sourcePath = $this->getSourcePath())) {
            return $this->console->error("Can't find the destination: <info>{$sourcePath}</info>");
        }
        
        if (! $this->getFilesystem()->isDirectory($destinationPath = $this->getDestinationPath())) {
            $this->getFilesystem()->makeDirectory($destinationPath, 0775, true);
        }
        
        if ($this->getFilesystem()->copyDirectory($sourcePath, $destinationPath)) {
            if ($this->showMessage === true) {
                $this->console->line("Published: <info>{$sourcePath}</info>");
            }
        } else {
            $this->console->error($this->error);
        }
    }
}

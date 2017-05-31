<?php
namespace Llama\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use Llama\Modules\Contracts\RepositoryInterface;
use Llama\Modules\Repository;

class ContractsServiceProvider extends ServiceProvider
{

    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, Repository::class);
    }
}

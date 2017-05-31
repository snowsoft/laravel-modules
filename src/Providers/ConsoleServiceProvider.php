<?php
namespace Llama\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use Llama\Modules\Commands\MakeCommandCommand;
use Llama\Modules\Commands\MakeControllerCommand;
use Llama\Modules\Commands\DisableCommand;
use Llama\Modules\Commands\DumpCommand;
use Llama\Modules\Commands\EnableCommand;
use Llama\Modules\Commands\MakeEventCommand;
use Llama\Modules\Commands\MakeJobCommand;
use Llama\Modules\Commands\MakeListenerCommand;
use Llama\Modules\Commands\MakeMailCommand;
use Llama\Modules\Commands\MakeMiddlewareCommand;
use Llama\Modules\Commands\MakeNotificationCommand;
use Llama\Modules\Commands\MakeProviderCommand;
use Llama\Modules\Commands\MakeRouteProviderCommand;
use Llama\Modules\Commands\InstallCommand;
use Llama\Modules\Commands\ListCommand;
use Llama\Modules\Commands\MakeModuleCommand;
use Llama\Modules\Commands\MakeRequestCommand;
use Llama\Modules\Commands\MigrateCommand;
use Llama\Modules\Commands\MigrateRefreshCommand;
use Llama\Modules\Commands\MigrateResetCommand;
use Llama\Modules\Commands\MigrateRollbackCommand;
use Llama\Modules\Commands\MakeMigrationCommand;
use Llama\Modules\Commands\MakeModelCommand;
use Llama\Modules\Commands\PublishAssetCommand;
use Llama\Modules\Commands\DbSeedCommand;
use Llama\Modules\Commands\MakeSeederCommand;
use Llama\Modules\Commands\SetupCommand;
use Llama\Modules\Commands\UpdateCommand;
use Llama\Modules\Commands\UseCommand;

class ConsoleServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The available commands
     *
     * @var array
     */
    protected $commands = [
        MakeModuleCommand::class,
        MakeCommandCommand::class,
        MakeControllerCommand::class,
        DisableCommand::class,
        EnableCommand::class,
        MakeEventCommand::class,
        MakeListenerCommand::class,
        MakeMiddlewareCommand::class,
        MakeProviderCommand::class,
        MakeRouteProviderCommand::class,
        InstallCommand::class,
        ListCommand::class,
        MigrateCommand::class,
        MigrateRefreshCommand::class,
        MigrateResetCommand::class,
        MigrateRollbackCommand::class,
        MakeMigrationCommand::class,
        MakeModelCommand::class,
        PublishAssetCommand::class,
        DbSeedCommand::class,
        MakeSeederCommand::class,
        SetupCommand::class,
        UpdateCommand::class,
        UseCommand::class,
        DumpCommand::class,
        MakeRequestCommand::class,
        MakeJobCommand::class,
        MakeMailCommand::class,
        MakeNotificationCommand::class
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     *
     * @return array
     */
    public function provides()
    {
        return $this->commands;
    }
}

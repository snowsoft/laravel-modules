# Laravel-Modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/llama-laravel/modules.svg?style=flat-square)](https://packagist.org/packages/llama-laravel/modules)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/llama-laravel/modules/master.svg?style=flat-square)](https://travis-ci.org/llama-laravel/modules)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/llama-laravel/modules.svg?maxAge=86400&style=flat-square)](https://scrutinizer-ci.com/g/llama-laravel/modules/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/25320a08-8af4-475e-a23e-3321f55bf8d2.svg?style=flat-square)](https://insight.sensiolabs.com/projects/25320a08-8af4-475e-a23e-3321f55bf8d2)
[![Quality Score](https://img.shields.io/scrutinizer/g/llama-laravel/modules.svg?style=flat-square)](https://scrutinizer-ci.com/g/llama-laravel/modules)
[![Total Downloads](https://img.shields.io/packagist/dt/llama-laravel/modules.svg?style=flat-square)](https://packagist.org/packages/llama-laravel/modules)


- [Upgrade Guide](#upgrade-guide)
- [Installation](#installation)
- [Configuration](#configuration)
- [Creating Module](#creating-a-module)
- [Artisan Commands](#artisan-commands)
- [Facades](#facades)
- [Entity](#entity)
- [Auto Scan Vendor Directory](#auto-scan-vendor-directory)
- [Publishing Modules](#publishing-modules)

`llama-laravel/modules` is a laravel package which created to manage your large laravel app using modules. Module is like a laravel package, it has some views, controllers or models. 

This package is supported and tested in Laravel 5.

This package was inspired by [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules).

## Upgrade Guide

## Installation

### Quick

To install through composer, simply run the following command:

``` bash
composer require llama-laravel/modules
```

#### Add Service Provider

Next add the following service provider in `config/app.php`.

```php
'providers' => [
  Llama\Modules\ModuleServiceProvider::class,
],
```

Next, add the following aliases to `aliases` array in the same file.

```php
'aliases' => [
  'Module' => Llama\Modules\Facades\Module::class,
],
```

Next publish the package's configuration file by running :

```
php artisan vendor:publish --provider="Llama\Modules\ModuleServiceProvider"
```

#### Autoloading

By default controllers, entities or repositories are not loaded automatically. You can autoload your modules using `psr-4`. For example :

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}
```

## Configuration

- `modules` - Used for save the generated modules.
- `assets` - Used for save the modules's assets from each modules.
- `migration` - Used for save the modules's migrations if you publish the modules's migrations.
- `seed` - Used for save the modules's seeds if you publish the modules's seeds.
- `generator` - Used for generate modules folders.
- `scan` - Used for allow to scan other folders.
- `enabled` - If `true`, the package will scan other paths. By default the value is `false`
- `paths` - The list of path which can scanned automatically by the package.
- `composer`
- `vendor` - Composer vendor name.
- `author.name` - Composer author name.
- `author.email` - Composer author email.
- `cache`
- `enabled` - If `true`, the scanned modules (all modules) will cached automatically. By default the value is `false`
- `key` - The name of cache.
- `lifetime` - Lifetime of cache.

## Creating A Module

To create a new module, you can simply run:

```
php artisan module:make <module-name>
```

- `<module-name>` - Required. The name of module will be created.

**Create a new module**

```
php artisan module:make Blog
```

**Create multiple modules**

```
php artisan module:make Blog User Auth
```

By default if you create a new module, that will add some resources like controller, seed class or provider automatically. If you don't want these, you can add `--plain` flag, to generate a plain module.

```shell
php artisan module:make Blog --plain
#OR
php artisan module:make Blog -p
```

**Naming Convention**

Because we are autoloading the modules using `psr-4`, we strongly recommend using `StudlyCase` convension.

**Folder Structure**

```
your-laravel/app/Modules/
  ├── Blog/
      ├── Config/
      ├── Database/
          ├── Seeds/
      ├── Http/
          ├── Controllers/
      ├── Providers/
      ├── Resources/
          ├── assets/
          ├── lang/
          ├── views/
      ├── Routes/
          ├── web.php
          ├── api.php
      ├── composer.json
      ├── module.json
```

## Artisan Commands

Setting up modules folders for first use

```
php artisan module:setup
```

Create new module.

```
php artisan module:make blog
```

Use the specified module.

```
php artisan module:use blog
```

Show all modules in command line.

```
php artisan module:list
```

Create new command for the specified module.

```
php artisan module:make-command CustomCommand blog
#OR
php artisan module:make-command CustomCommand --command=custom:command blog
```

Create new migration for the specified module.

```
php artisan module:make-migration create_users_table blog
#OR
php artisan module:make-migration create_users_table blog --create=users
#OR
php artisan module:make-migration add_email_to_users_table blog --table=users
```

Rollback, Reset and Refresh The Modules Migrations.

```
php artisan module:migrate-rollback
#OR
php artisan module:migrate-reset
#OR
php artisan module:migrate-refresh
```

Rollback, Reset and Refresh The Migrations for the specified module.

```
php artisan module:migrate-rollback blog
#OR
php artisan module:migrate-reset blog
#OR
php artisan module:migrate-refresh blog
```

Migrate from the specified module.

```
php artisan module:migrate blog
```

Migrate from all modules.

```
php artisan module:migrate
```

Create new seeder for the specified module.

```
php artisan module:make-seeder PostsTableSeeder blog
```

Seed from the specified module.

```
php artisan module:db-seed blog
```

Seed from all modules.

```
php artisan module:db-seed
```

Create new controller for the specified module.

```
php artisan module:make-controller SiteController blog
```

Publish assets from the specified module to public directory.

```
php artisan module:publish-asset blog
```

Publish assets from all modules to public directory.

```
php artisan module:publish-asset
```

Create new model for the specified module.

```
php artisan module:make-model User blog
```

Create new service provider for the specified module.

```
php artisan module:make-provider MyServiceProvider blog
```

Create new policy for the specified module.

```
php artisan module:make-policy PostsPolicy blog
```

Create new route provider for the specified module.

```
php artisan module:make-route blog
```

Create new form request for the specified module.

```
php artisan module:make-request CreateRequest blog
```

Create new event for the specified module.

```
php artisan module:make-event CreateEvent blog
```

Create new job for the specified module.

```
php artisan module:make-job CreateJob blog
```

Create new listener for the specified module.

```
php artisan module:make-listener CreateListener blog --event="App\Modules\Blog\Events\CreateEvent"
```

Create new middleware for the specified module.

```
php artisan module:make-middleware CreateNewPostMiddleware blog
```

Create new mail for the specified module.

```
php artisan module:make-mail WelcomeEmail checkout
#OR
php artisan module:make-mail WelcomeEmail checkout --markdown=emails.checkout.shipped
```

Create new notification for the specified module.

```
php artisan module:make-notification InvoicePaid checkout
#OR
php artisan module:make-notification InvoicePaid checkout --markdown=notifications.checkout.shipped
```

Enable the specified module.


```
php artisan module:enable blog
```

Disable the specified module.

```
php artisan module:disable blog
```

Update dependencies for the specified module.

```
php artisan module:update blog
```

Update dependencies for all modules.

```
php artisan module:update
```

Show the list of modules.

```
php artisan module:list
```

## Facades

### Using Model Factories

Normally, you can use [model factories](https://laravel.com/docs/5.4/database-testing#writing-factories) to conveniently generate large amounts of database records.
I have defined new way to writing seeder for the specified module.

```php
Module::factory(App\User::class, 50)->create()->each(function ($u) {
    $u->posts()->save(factory(App\Post::class)->make());
});
```

Get all modules.

```php
Module::all();
```

Get all cached modules.

```php
Module::getCached()
```

Get ordered modules. The modules will be ordered by the `priority` key in `module.json` file.

```php
Module::getOrdered();
```

Get scanned modules.

```php
Module::scan();
```

Find a specific module.

```php
Module::find('name');
#OR
Module::get('name');
```

Find a module, if there is one, return the `Module` instance, otherwise throw `Llama\Modules\Exeptions\ModuleNotFoundException`.

```php
Module::findOrFail('module-name');
```

Get scanned locations.

```php
Module::getScannedLocations();
```

Get all modules as a collection instance.

```php
Module::toCollection();
```

Get modules by the status. 1 for active and 0 for inactive.

```php
Module::getByStatus(1);
```

Check the specified module. If it exists, will return `true`, otherwise `false`.

```php
Module::has('blog');
```

Get all enabled modules.

```php
Module::activated();
```

Get all disabled modules.

```php
Module::deactivated();
```

Get count of all modules.

```php
Module::count();
```

Get module path.

```php
Module::getPath();
```

Register the modules.

```php
Module::register();
```

Boot all available modules.

```php
Module::boot();
```

Get all enabled modules as collection instance.

```php
Module::collections();
```

Get module path from the specified module.

```php
Module::getModulePath('name');
```

Get assets path from the specified module.

```php
Module::assetPath('name');
```

Get config value from this package.

```php
Module::config('composer.vendor');
```

Get used storage path.

```php
Module::getUsedStoragePath();
```

Get used module for cli session.

```php
Module::used();
```

Set used module for cli session.

```php
Module::used('name');
```

Get modules's assets path.

```php
Module::getAssetsPath();
```

Get modules's namespace.

```php
Module::getNamespace();
```

Get asset url from specific module.

```php
Module::asset('blog::img/logo.img');
```

Install the specified module by given module name.

```php
Module::install('llama-laravel-modules/hello');
```

Update dependencies for the specified module.

```php
Module::update('hello');
```

## Entity

Get an entity from a specific module.

```php
$module = Module::find('blog');
```

Get module name.

```php
$module->getName();
```

Get module name in lowercase.

```php
$module->getLowerName();
```

Get module name in studlycase.

```php
$module->getStudlyName();
```

Get module path.

```php
$module->getPath();
```

Get extra path.

```php
$module->getExtraPath('assets');
```

Disable the specified module.

```php
$module->disable();
```

Enable the specified module.

```php
$module->enable();
```

Delete the specified module.

```php
$module->delete();
```

Get namespace specified module.

```php
$module->getNamespace();
```

## Custom Namespaces

When you create a new module it also registers new custom namespace for `Lang`, `View` and `Config`. For example, if you create a new module named blog, it will also register new namespace/hint blog for that module. Then, you can use that namespace for calling `Lang`, `View` or `Config`. Following are some examples of its usage:

Calling Lang:

```php
Lang::get('blog::group.name');
#OR
trans('blog::group.name');
```

Calling View:

```php
View::make('blog::index');
#OR
View::make('blog::partials.sidebar');
```

Calling Config:

```php
Config::get('blog.name');
#OR
config('blog.name');
```

## Publishing Modules

Have you created a laravel modules? Yes, I've. Then, I want to publish my modules. Where do I publish it? That's the question. What's the answer ? The answer is [Packagist](http://packagist.org).

### Auto Scan Vendor Directory

By default the `vendor` directory is not scanned automatically, you need to update the configuration file to allow that. Set `scan.enabled` value to `true`. For example :

```php
// file config/modules.php

return [
  //...
  'scan' => [
    'enabled' => true
  ]
  //...
];
```

You can verify the module has been installed using `module:list` command:

```
php artisan module:list
```

## Publishing Modules

After creating a module and you are sure your module module will be used by other developers. You can push your module to [github](https://github.com) or [bitbucket](https://bitbucket.org) and after that you can submit your module to the packagist website.

You can follow this step to publish your module.

1. Create A Module.
2. Push the module to github.
3. Submit your module to the packagist website.

Submit to packagist is very easy, just give your github repository, click submit and you done.


## Credits

- [XuaNguyen](https://github.com/xuanhoa88)
- [All Contributors](../../contributors)

## About XuaNguyen

XuaNguyen is a freelance web developer specialising on the laravel framework.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

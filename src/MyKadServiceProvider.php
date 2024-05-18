<?php

namespace FikriMastor\MyKad;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use FikriMastor\MyKad\Commands\MyKadCommand;

class MyKadServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mykad')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_mykad_table')
            ->hasCommand(MyKadCommand::class);
    }
}

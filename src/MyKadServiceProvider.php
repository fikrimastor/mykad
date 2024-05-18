<?php

namespace FikriMastor\MyKad;

use FikriMastor\MyKad\Commands\MyKadCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

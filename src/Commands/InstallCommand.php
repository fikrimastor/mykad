<?php

namespace FikriMastor\MyKad\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'mykad:install';

    protected $description = 'Check the installation status of optional mykad dependencies';

    public function handle(): int
    {
        $this->components->info('MyKad — Optional Dependency Check');
        $this->newLine();

        $optionalDeps = $this->getOptionalDependencies();

        $this->table(
            ['Package', 'Status', 'Enables', 'Install Command'],
            $optionalDeps['rows'],
        );

        $this->newLine();

        if (empty($optionalDeps['missing'])) {
            $this->components->info('All optional dependencies are installed. You\'re all set!');

            return self::SUCCESS;
        }

        $this->components->warn('Some optional dependencies are not installed:');

        foreach ($optionalDeps['missing'] as $package => $installCmd) {
            $this->line("  <fg=cyan>{$installCmd}</>");
        }

        $this->newLine();
        $this->line('  The package works without optional dependencies.');
        $this->line('  Install them to unlock additional features.');

        return self::SUCCESS;
    }

    /**
     * @return array{rows: list<array<string>>, missing: array<string, string>}
     */
    private function getOptionalDependencies(): array
    {
        $rows = [];
        $missing = [];

        foreach ($this->optionalPackages() as $package => $meta) {
            $installed = InstalledVersions::isInstalled($package);
            $version = $installed ? InstalledVersions::getPrettyVersion($package) : '—';
            $status = $installed
                ? "<fg=green>✓ installed ({$version})</>"
                : '<fg=yellow>✗ not installed</>';

            $rows[] = [$package, $status, $meta['enables'], "composer require {$package}"];

            if (! $installed) {
                $missing[$package] = "composer require {$package}";
            }
        }

        return compact('rows', 'missing');
    }

    /**
     * @return array<string, array{enables: string}>
     */
    private function optionalPackages(): array
    {
        return [
            'nesbot/carbon' => [
                'enables' => 'Carbon date objects for date_of_birth output',
            ],
        ];
    }
}

<?php

namespace FikriMastor\MyKad\Commands;

use Illuminate\Console\Command;

class MyKadCommand extends Command
{
    public $signature = 'mykad';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

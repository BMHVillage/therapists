<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console\Command;

use Ghostwriter\Filesystem\Filesystem;
use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Console\Command\Command;
use Throwable;

abstract class AbstractCommand extends Command
{
    /** @throws Throwable */
    public function __construct(
        public readonly Filesystem $filesystem,
        public readonly Spreadsheet $spreadsheet,
        public readonly Manager $manager,
        public readonly Client $guzzle,
    ) {
        parent::__construct();
    }
}

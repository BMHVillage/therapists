<?php

declare(strict_types=1);

use BlackMentalHealthVillage\Therapists\Console\Command;

return [
    'name' => 'Therapists',
    'package' => 'bmhv/therapists',
    'commands' => [
        'scrape' => Command\ScrapeCommand::class,
        // 'readme' => Command\ReadmeCommand::class,
        'csv' => Command\CsvCommand::class,
    ],
];

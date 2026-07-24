<?php

declare(strict_types=1);

use BlackMentalHealthVillage\Therapists\Console\Command\CsvCommand;
use BlackMentalHealthVillage\Therapists\Console\Command\ReadmeCommand;
use BlackMentalHealthVillage\Therapists\Console\Command\ScrapeCommand;

/**
 * @return array{
 *     name:string,
 *     package:string,
 *     commands:array<string,class-string>,
 *     auto_exit:bool,
 *     catch_errors:bool,
 *     catch_exceptions:bool,
 *     default_command:bool|class-string,
 *     single_command:bool
 * }
 */
return [
    'name' => 'Therapists',
    'package' => 'bmhv/therapists',
    'commands' => [
        'scrape' => ScrapeCommand::class,
        'readme' => ReadmeCommand::class,
        'csv' => CsvCommand::class,
    ],
    'auto_exit'       => false,
    'catch_errors'     => false,
    'catch_exceptions' => false,
    'default_command' => false,
    'single_command' => false,
];

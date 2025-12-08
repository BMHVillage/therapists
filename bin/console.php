#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console;

use ErrorException;
use Ghostwriter\Container\Container;
use Symfony\Component\Console\Application;

use const DIRECTORY_SEPARATOR;
use const STDERR;

use function dirname;
use function file_exists;
use function fwrite;
use function implode;
use function restore_error_handler;
use function set_error_handler;

/** @var ?string $_composer_autoload_path */
(static function (string $autoloader): never {
    if (! file_exists($autoloader)) {
        fwrite(STDERR, '[ERROR]Cannot locate "' . $autoloader . '"\n please run "composer install"\n');
        exit(254);
    }

    set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
        throw new ErrorException($message, 255, $severity, $file, $line);
    });

    require $autoloader;

    restore_error_handler();

    /** #BlackLivesMatter */
    exit(Container::getInstance()->get(Application::class)->run());
})($_composer_autoload_path ?? implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']));

<?php

declare(strict_types=1);

namespace Ghostwriter\TnTherapists;

use ErrorException;
use Ghostwriter\TnTherapists\Model\Therapist;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Database;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

use function collect;
use function dirname;
use function dump;
use function file_exists;
use function fwrite;
use function set_error_handler;
use function sprintf;
use function str_replace;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use const STDERR;

(static function (string $autoloader): void {
    set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
        throw new ErrorException($message, 255, $severity, $file, $line);
    });

    if (! file_exists($autoloader)) {
        fwrite(
            STDERR,
            sprintf('[ERROR]Cannot locate "%s"%s please run "composer install"%s', $autoloader, PHP_EOL, PHP_EOL)
        );
        exit;
    }

    require $autoloader;

    $filesystem = new Filesystem();

    $databasePath = './database.sqlite';

    if ($filesystem->missing($databasePath)) {
        $filesystem->put($databasePath, '');
    }

    $database = new Database();
    $database->addConnection([
        'driver'    => 'sqlite',
        'database' => 'database.sqlite',
        'prefix' => '',
    ]);
    $database->setEventDispatcher(new Dispatcher(new Container()));
    $database->setAsGlobal();
    $database->bootEloquent();

    $tableTemplate = $filesystem->get('./table.html');

    $filesystem->put(
        './README.md',
        str_replace(
            '{therapists}',
            collect([
                '> Collection of Black and African American Therapists in Nashville, TN and Therapists serving Black and African American communities.',

                PHP_EOL,
                '> Publicly available information collected to help promote Healing Ourselves and Healing Others. #BlackLivesMatter',
                PHP_EOL,
            ])->merge(
                Therapist::all()
                    ->sortBy('title')
                    ->collect()
                    ->map(
                        static fn (Therapist $therapist): mixed => dump(sprintf(
                            $tableTemplate,
                            $therapist->getAttribute('subtitle'),
                            $therapist->getAttribute('hash'),
                            $therapist->getAttribute('image'),
                            $therapist->getAttribute('title'),
                            $therapist->getAttribute('statement'),
                            $therapist->getAttribute('offersOnlineTherapy'),
                            $therapist->getAttribute('acceptingAppointments'),
                            $therapist->getAttribute('location'),
                            $therapist->getAttribute('contact'),
                        ))
                    )
            )->join(PHP_EOL),
            $filesystem->get('./README.md.tmp')
        )
    );
})(\implode(DIRECTORY_SEPARATOR,[__DIR__ , 'vendor','autoload.php']));

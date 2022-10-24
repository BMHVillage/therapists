<?php

declare(strict_types=1);

namespace Ghostwriter\TnTherapists;

use Ghostwriter\TnTherapists\Model\Therapist;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Database;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

(static function () {
    if (file_exists($a = __DIR__ . '/vendor/autoload.php')) {
        require $a;
    } elseif (file_exists($a = __DIR__ . '/../../../autoload.php')) {
        require $a;
    } elseif (file_exists($a = __DIR__ . '/../vendor/autoload.php')) {
        require $a;
    } elseif (file_exists($a = __DIR__ . '/../autoload.php')) {
        require $a;
    } else {
        fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
        exit(1);
    }

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
                Therapist::all()->sortBy('title')->collect()->map(static fn (Therapist $therapist) => dump(sprintf(
                    $tableTemplate,
                    $therapist->getAttribute('subtitle'),
                    $therapist->getAttribute('hash'),
                    str_replace(
                        'https://',
                        'https://i1.wp.com/',
                        $therapist->getAttribute('image')??'https://i.imgur.com/D77KqTJ.png'
                    ),
                    $therapist->getAttribute('title'),
                    $therapist->getAttribute('statement'),
                    $therapist->getAttribute(
                        'offersOnlineTherapy'
                    ) === 'Offers online therapy' ? 'In-person/Online therapy' : 'In-person therapy',
                    $therapist->getAttribute(
                        'acceptingAppointments'
                    )  === 'Not accepting new clients' ? 'Not accepting new clients' : 'Accepting appointments',
                    $therapist->getAttribute('location'),
                    $therapist->getAttribute('contact'),
                )))
            )->join(PHP_EOL),
            $filesystem->get('./README.md.tmp')
        )
    );
})();

<?php

declare(strict_types=1);

namespace Ghostwriter\TnTherapists;

use Ghostwriter\TnTherapists\Model\Therapist;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Database;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

(static function () {
    /** @var null|string $path */
    $path = array_reduce(
        [
            __DIR__ . '/vendor/autoload.php',
            __DIR__ . '/../../../autoload.php',
            __DIR__ . '/../vendor/autoload.php',
            __DIR__ . '/../autoload.php',
        ],
        static fn ($carry, $item) => (($carry === null) && file_exists($item)) ? $item : $carry
    );
    if ($path === null) {
        fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
        exit(1);
    }
    require $path;

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

    $handle = fopen('Therapist.csv', 'wb');

    Therapist::getQuery()
        ->whereNot('contact', '')
        ->orderBy('title')
        ->chunk(100, static function ($rows) use ($handle) {
        foreach ($rows as $row) {
            $row = (array) $row;
            fputcsv(
                $handle,
                [
                    'id'=> $row['id'],
                    'title'=> $row['title'],
                    'subtitle'=> $row['subtitle'],
                    'statement'=> $row['statement'],
                    'image'=> $row['image'],
                    'contact'=> $row['contact'],
                    'location'=> $row['location'],
                    'offersOnlineTherapy'=> $row['offersOnlineTherapy'],
                    'acceptingAppointments'=> $row['acceptingAppointments'],
                ],
                ';'
            );
        }
    });

    fclose($handle);
})();

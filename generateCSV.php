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

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $worksheet = $spreadsheet->getActiveSheet();
    $worksheet->fromArray(
        Therapist::whereNot('contact', '')
            ->orderBy('title')
            ->get()
            ->toArray()
    );
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
    $writer->save("Therapist.csv");
})();

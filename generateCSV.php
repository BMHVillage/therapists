<?php

declare(strict_types=1);

namespace Ghostwriter\TnTherapists;

use ErrorException;
use Ghostwriter\TnTherapists\Model\Therapist;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Database;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use function array_reduce;
use function file_exists;
use function fwrite;

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
        exit(1);
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

    $heading = ['Name', 'Title', 'Bio', 'Photo', 'Contact', 'Location', 'Type', 'Status'];

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getActiveSheet()->fromArray(
        Therapist::whereNot('contact', '')
            ->orderBy('title')
            ->get()
            ->prepend($heading)
            ->toArray()
    );
    $writer = new Csv($spreadsheet);
    $writer->save('Therapist.csv');
})(\implode(DIRECTORY_SEPARATOR,[__DIR__ , 'vendor','autoload.php']));

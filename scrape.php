<?php

declare(strict_types=1);

namespace Ghostwriter\TnTherapists;

use Ghostwriter\TnTherapists\Model\Therapist;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Database;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Factory;
use Symfony\Component\DomCrawler\Crawler;

use function basename;
use function dump;
use function fwrite;
use function is_file;
use function str_replace;
use function trim;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use const STDERR;

(static function (string $composerAutoloadPath): void {
    if (! is_file($composerAutoloadPath)) {
        fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
        exit(1);
    }

    require $composerAutoloadPath;

    $filesystem = new Filesystem();
    $databasePath = './database.sqlite';

    if ($filesystem->missing($databasePath)) {
        $filesystem->put($databasePath, '');
    }

    $database = new Database();
    $database->addConnection([
        'driver'    => 'sqlite',
        'database' => $databasePath,
        'prefix' => '',
    ]);
    $database->setEventDispatcher(new Dispatcher(new Container()));
    $database->setAsGlobal();
    $database->bootEloquent();

    //    TODO: add Endorsed Therapists
    // (list of therapists who have been endorsed/approved by the community
    // (list of therapists who have been recommended by other therapists)

    if (! Database::schema()->hasTable('therapists')) {
        Database::schema()->create('therapists', static function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('title')
                ->nullable();
            $blueprint->string('subtitle')
                ->nullable();
            $blueprint->text('statement')
                ->nullable();
            $blueprint->string('image')
                ->nullable();
            $blueprint->string('contact')
                ->nullable();
            $blueprint->string('location')
                ->nullable();
            $blueprint->string('offersOnlineTherapy')
                ->nullable();
            $blueprint->string('acceptingAppointments')
                ->nullable();
            $blueprint->string('hash')
                ->unique()
                ->index();
            $blueprint->softDeletes();
            $blueprint->timestamps();
        });
        echo 'table created';
    } else {
        echo 'table loaded';
    }

    $api = 'https://www.psychologytoday.com/us/therapists/tn/nashville';
    $http = new Factory();
    $page = 100;

    do {
        $response = $http->get($api, [
            'category' => 'african-american',
            'page' => $page,
        ]);
        $crawler = new Crawler($response->body());
        $crawler->filter('div.results-row')
            ->each(static function (Crawler $crawler) {
                $hash = basename($crawler->filter('a')->links()[0]->getUri());

                $title = $crawler->filter('.profile-title');
                $title = $title->count()>0 ? $title->innerText() : null;

                $subtitle = $crawler->filter('.profile-subtitle-credentials');
                $subtitle = $subtitle->count()>0 ? $subtitle->innerText() : null;

                $contact = $crawler->filter('.results-row-phone');
                $contact = $contact->count()>0 ? $contact->innerText() : '';

                $statements = $crawler->filter('div.statements');
                $statement = $statements->count()>0 ? $statements->text() : null;

                $profileLocation = $crawler->filter('.profile-location');
                $location = $profileLocation->count()>0 ? $profileLocation->text() : '';

                $profileImage = $crawler->filter('span.profile-image img');
                $image = ($profileImage->count()>0) ? ($profileImage->attr('src') ?? $profileImage->attr(
                    'data-src'
                )) : null;

                $profileTeletherapy = $crawler->filter('.profile-teletherapy');
                $offersOnlineTherapy = $profileTeletherapy->count()>0 ? $profileTeletherapy->text() : null;

                $acceptingAppointments = $crawler->filter('.accepting-appointments');
                $isAcceptingAppointments = $acceptingAppointments->count()>0 ? $acceptingAppointments->text() : null;

                $payload = [
                    'hash' => $hash,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'image' => str_replace(
                        'https://',
                        'https://i1.wp.com/',
                        $image ?? 'https://i.imgur.com/D77KqTJ.png'
                    ),
                    'statement' => $statement,
                    'contact' => trim(str_replace("\u{a0}", '', $contact)),
                    'location' => trim(str_replace(['Office is near:', "\u{a0}"], ['', ' '], $location)),
                    'offersOnlineTherapy' => ('Offers online therapy' === $offersOnlineTherapy)
                        ? 'In-person/Online therapy'
                        : 'In-person therapy',
                    'acceptingAppointments' => $isAcceptingAppointments ?? 'Accepting clients',
                ];

                dump($payload);

                return Therapist::withTrashed()->updateOrCreate([
                    'hash'=> $hash,
                ], $payload);
            });
    } while (--$page);
})(\implode(DIRECTORY_SEPARATOR,[__DIR__ , 'vendor','autoload.php']));

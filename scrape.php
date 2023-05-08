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

(static function () {
    /** @var null|string $path */
    $path = array_reduce(
        [
            __DIR__ . '/vendor/autoload.php',
            __DIR__ . '/../autoload.php',
            __DIR__ . '/../../../autoload.php',
            __DIR__ . '/../vendor/autoload.php',
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
        'database' => $databasePath,
        'prefix' => '',
    ]);
    $database->setEventDispatcher(new Dispatcher(new Container()));
    $database->setAsGlobal();
    $database->bootEloquent();

    if (! Database::schema()->hasTable('therapists')) {
        Database::schema()->create('therapists', static function (Blueprint $table) {
            $table->id();
            $table->string('title')
                ->nullable();
            $table->string('subtitle')
                ->nullable();
            $table->text('statement')
                ->nullable();
            $table->string('image')
                ->nullable();
            $table->string('contact')
                ->nullable();
            $table->string('location')
                ->nullable();
            $table->string('offersOnlineTherapy')
                ->nullable();
            $table->string('acceptingAppointments')
                ->nullable();
            $table->string('hash')
                ->unique()
                ->index();
            $table->softDeletes();
            $table->timestamps();
        });
        echo 'table created';
    } else {
        echo 'table loaded';
    }

    $api = 'https://www.psychologytoday.com/us/therapists/tn/nashville';
    $http = new Factory();
    $page = 10;

    do {
        $response = $http->get($api, [
            'category' => 'african-american',
            'page' => $page,
        ]);
        $crawler = new Crawler($response->body());
        $crawler->filter('div.results-row')
            ->each(static function (Crawler $node) {
                $hash = basename($node->filter('a')->links()[0]->getUri());

                $title = $node->filter('.profile-title');
                $title = $title->count()>0 ? $title->innerText() : null;

                $subtitle = $node->filter('.profile-subtitle');
                $subtitle = $subtitle->count()>0 ? $subtitle->innerText() : null;

                $contact = $node->filter('.results-row-mob');
                $contact = $contact->count()>0 ? $contact->innerText() : '';

                $statement = $node->filter('div.statements');
                $statement = $statement->count()>0 ? $statement->innerText() : null;

                $location = $node->filter('.profile-location');
                $location = $location->count()>0 ? $location->text() : null;

                $image = $node->filter('span.profile-image img');
                $image = ($image->count()>0) ? ($image->attr('src') ?? $image->attr('data-src')) : null;

                $offersOnlineTherapy = $node->filter('.profile-teletherapy');
                $offersOnlineTherapy = $offersOnlineTherapy->count()>0 ? $offersOnlineTherapy->text() : null;

                $acceptingAppointments = $node->filter('.accepting-appointments');
                $acceptingAppointments = $acceptingAppointments->count()>0 ? $acceptingAppointments->text() : null;
                return Therapist::withTrashed()->updateOrCreate(
                    [
                        'hash'=> $hash,
                    ],
                    dump([
                        'hash'=> $hash,
                        'title'=>$title,
                        'subtitle'=>$subtitle,
                        'image' => str_replace(
                            'https://',
                            'https://i1.wp.com/',
                            $image ??'https://i.imgur.com/D77KqTJ.png'
                        ),
                        'statement'=> $statement,
                        'contact'=> trim(str_replace(' ', '', $contact)),
                        'location'=> trim(str_replace('Office is near:', '', $location)),
                        'offersOnlineTherapy'=>  ($offersOnlineTherapy === 'Offers online therapy') ?
                            'In-person/Online therapy' :
                            'In-person therapy',
                        'acceptingAppointments'=>  $acceptingAppointments ?? 'Accepting clients',
                    ])
                );
            });
    } while (--$page);
})();

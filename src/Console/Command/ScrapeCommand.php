<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console\Command;

use BlackMentalHealthVillage\Therapists\Model\Therapist;
use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Override;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

use const PHP_EOL;
use const STDERR;

use function array_key_exists;
use function basename;
use function dump;
use function fwrite;
use function http_build_query;
use function mb_trim;
use function random_int;
use function sleep;
use function str_replace;

/** @see ScrapeCommandTest */
final class ScrapeCommand extends Command
{
    /** @throws Throwable */
    public function __construct(
        private readonly Manager $manager,
        private readonly Client $guzzle,
    ) {
        parent::__construct('scrape');
        $this->setDescription('Find therapists in Tennessee from public sources.');
    }

    /** @throws Throwable */
    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $schemaBuilder = $this->manager->getDatabaseManager()->getSchemaBuilder();
        if (! $schemaBuilder->hasTable('therapists')) {
            $schemaBuilder->create('therapists', static function (Blueprint $blueprint): void {
                $blueprint->id();
                $blueprint->string('title')->nullable();
                $blueprint->string('subtitle')->nullable();
                $blueprint->text('statement')->nullable();
                $blueprint->string('image')->nullable();
                $blueprint->string('contact')->nullable();
                $blueprint->string('location')->nullable();
                $blueprint->string('offersOnlineTherapy')->nullable();
                $blueprint->string('acceptingAppointments')->nullable();
                $blueprint->string('hash')->unique()->index();
                $blueprint->softDeletes();
                $blueprint->timestamps();
            });

            $output->writeln('Therapists table created.');
        } else {
            $output->writeln('Therapists table loaded.');
        }

        $api = 'https://www.psychologytoday.com/us/therapists/tn/nashville';

        $page = 1;

        do {
            $query = [
                'category' => 'african-american',
                'page' => $page,
            ];

            $response = $this->guzzle->get($api, [
                'timeout' => 10,
                'query' => $query,
            ]);

            $body = (string) $response->getBody();

            if ($this->failed($response)) {
                fwrite(STDERR, 'Failed to fetch data from the API' . PHP_EOL);
                fwrite(STDERR, $body . PHP_EOL);
                exit(1);
            }

            (new Crawler($body))
                ->filter('div.results-row')
                ->each(static function (Crawler $crawler): void {
                    $links = $crawler->filter('a')->links();

                    if (! array_key_exists(0, $links)) {
                        return;
                    }

                    $hash = basename($links[0]->getUri());

                    $title = $crawler->filter('.profile-title');
                    $title = ($title->count()>0) ? $title->innerText() : null;

                    $subtitle = $crawler->filter('.profile-subtitle-credentials');
                    $subtitle = ($subtitle->count()>0) ? $subtitle->innerText() : null;

                    $contact = $crawler->filter('.results-row-phone');
                    $contact = ($contact->count()>0) ? $contact->innerText() : '';

                    $statements = $crawler->filter('div.statements');
                    $statement = ($statements->count()>0) ? $statements->text() : null;

                    $profileLocation = $crawler->filter('.profile-location');
                    $location = ($profileLocation->count()>0) ? $profileLocation->text() : '';

                    $profileImage = $crawler->filter('span.profile-image img');
                    $image = ($profileImage->count() > 0)
                        ? ($profileImage->attr('src') ?? $profileImage->attr('data-src'))
                        : null;

                    $profileTeletherapy = $crawler->filter('.profile-teletherapy');
                    $offersOnlineTherapy = ($profileTeletherapy->count()>0) ? $profileTeletherapy->text() : null;

                    $acceptingAppointments = $crawler->filter('.accepting-appointments');
                    $isAcceptingAppointments = ($acceptingAppointments->count()>0) ? $acceptingAppointments->text() : null;

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
                        'contact' => mb_trim(str_replace("\u{a0}", '', $contact)),
                        'location' => mb_trim(str_replace(['Office is near:', "\u{a0}"], ['', ' '], $location)),
                        'offersOnlineTherapy' => ('Offers online therapy' === $offersOnlineTherapy)
                            ? 'In-person/Online therapy'
                            : 'In-person therapy',
                        'acceptingAppointments' => $isAcceptingAppointments ?? 'Accepting clients',
                    ];

                    dump($payload);

                    Therapist::withTrashed()->updateOrCreate([
                        'hash'=> $hash,
                    ], $payload);
                });

            $seconds = random_int(3, 15);

            $output->writeln('Fetched: ' . $api . '?' . http_build_query($query));
            $output->writeln('Sleeping for ' . $seconds . ' seconds to avoid rate limiting...');

            sleep($seconds);
        } while (++$page);

        return self::SUCCESS;
    }

    /** Determine if the response indicates a client error occurred. */
    private function clientError(ResponseInterface $response): bool
    {
        return $this->status($response) >= 400 && $this->status($response) < 500;
    }

    /** Determine if the response indicates a client or server error occurred. */
    private function failed(ResponseInterface $response): bool
    {
        return $this->serverError($response) || $this->clientError($response);
    }

    /** Determine if the response indicates a server error occurred. */
    private function serverError(ResponseInterface $response): bool
    {
        return $this->status($response) >= 500;
    }

    private function status(ResponseInterface $response): int
    {
        return $response->getStatusCode();
    }
}

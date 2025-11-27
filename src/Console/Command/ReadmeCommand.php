<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console\Command;

use BlackMentalHealthVillage\Therapists\Model\Therapist;
use Ghostwriter\Filesystem\Filesystem;
use Override;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

use function collect;
use function implode;
use function sprintf;
use function str_replace;

/** @see ReadmeCommandTest */
final class ReadmeCommand extends Command
{
    /** @throws Throwable */
    public function __construct(
        private readonly Filesystem $filesystem,
    ) {
        parent::__construct('readme');
        $this->setDescription('Generate the README file for this project.');
    }

    /** @throws Throwable */
    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workspace = $this->filesystem->currentWorkingDirectory();

        $tableTemplate = $this->filesystem->read(implode(DIRECTORY_SEPARATOR, [$workspace, 'templates', 'table.html']));

        $readmePath = implode(DIRECTORY_SEPARATOR, [$workspace, 'README.md']);
        $readmeTmpPath = implode(DIRECTORY_SEPARATOR, [$workspace, 'templates', 'README.md.tmp']);

        $contents = str_replace(
            '{therapists}',
            collect([
                '> Collection of Black and African American Therapists in Nashville, TN and Therapists serving Black and African American communities.',

                PHP_EOL,
                '> Publicly available information collected to help promote Healing Ourselves and Healing Others. #BlackLivesMatter',
                PHP_EOL,
            ])->merge(
                Therapist::query()
                    ->where('title', '!=', '')
                    ->where('contact', '!=', '')
                    ->orderBy('title')
                    ->get()
                    ->map(
                        static fn (Therapist $therapist): string => sprintf(
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
                        )
                    )
            )->join(PHP_EOL),
            $this->filesystem->read($readmeTmpPath)
        );

        $this->filesystem->write($readmePath, $contents);

        return self::SUCCESS;
    }
}

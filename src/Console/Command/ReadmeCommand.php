<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console\Command;

use BlackMentalHealthVillage\Therapists\Model\Therapist;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function base_path;
use function collect;
use function sprintf;
use function str_repeat;
use function str_replace;
use function template_path;

/**
 * @see ReadmeCommandTest
 */
#[AsCommand(name: 'readme', description: 'Generate the README file for this project.')]
final class ReadmeCommand extends AbstractCommand
{
    /** @throws Throwable */
    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([$this->getName(), str_repeat('=', 8), $this->getDescription()]);

        $tableTemplate = $this->filesystem->read(template_path('table.html'));

        $this->filesystem->write(
            base_path('README.md'),
            str_replace(
                [
                    "\xE2\x80\xA8",
                    '{therapists}',
                ],
                [
                    '',
                    collect([
                        '> Collection of Black and African American Therapists in Nashville, TN and Therapists serving Black and African American communities.',
                        "\n",
                        '> Publicly available information collected to help promote Healing Ourselves and Healing Others. #BlackLivesMatter',
                        "\n",
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
                    )->join("\n"),
                ],
                $this->filesystem->read(template_path('README.md.tmp'))
            )
        );

        return self::SUCCESS;
    }
}

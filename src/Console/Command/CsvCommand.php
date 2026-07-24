<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console\Command;

use BlackMentalHealthVillage\Therapists\Model\Therapist;
use Override;
use PhpOffice\PhpSpreadsheet\Writer\Csv as PhpOfficeCsvWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function storage_path;
use function str_repeat;

/**
 * @see CsvCommandTest
 */
#[AsCommand(name: 'csv', description: 'Generate a CSV file of therapists in Tennessee.')]
final class CsvCommand extends AbstractCommand
{
    /** @throws Throwable */
    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([$this->getName(), str_repeat('=', 8), $this->getDescription()]);

        $filename = storage_path('Therapist.csv');

        if (! $this->filesystem->exists($filename)) {
            $this->filesystem->write($filename, '');
        }

        $output->writeln('Writing to ' . $filename);

        $collection = Therapist::query()
            ->where('title', '!=', '')
            ->where('contact', '!=', '')
            ->orderBy('title')
            ->get();

        $output->writeln('Found ' . $collection->count() . ' records.');

        $heading = ['Name', 'Title', 'Bio', 'Photo', 'Contact', 'Location', 'Type', 'Status'];

        $this->spreadsheet->getActiveSheet()->fromArray($collection->prepend($heading)->toArray());

        (new PhpOfficeCsvWriter($this->spreadsheet))->save($filename);

        $output->writeln('CSV generated successfully.');

        return self::SUCCESS;
    }
}

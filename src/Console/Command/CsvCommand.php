<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Console\Command;

use BlackMentalHealthVillage\Therapists\Model\Therapist;
use Ghostwriter\Filesystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use const DIRECTORY_SEPARATOR;

use function implode;

/** @see CsvCommandTest */
final class CsvCommand extends Command
{
    /** @throws Throwable */
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Spreadsheet $spreadsheet,
    ) {
        parent::__construct('csv');
        $this->setDescription('Generate a CSV file of therapists in Tennessee.');
    }

    /** @throws Throwable */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workspace = $this->filesystem->currentWorkingDirectory();

        $filename = implode(DIRECTORY_SEPARATOR, [$workspace, 'storage', 'Therapist.csv']);

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

        (new Csv($this->spreadsheet))->save($filename);

        $output->writeln('CSV generated successfully.');

        return self::SUCCESS;
    }
}

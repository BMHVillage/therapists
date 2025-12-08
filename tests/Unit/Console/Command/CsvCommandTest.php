<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use BlackMentalHealthVillage\Therapists\Console\Command\CsvCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(CsvCommand::class)]
final class CsvCommandTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

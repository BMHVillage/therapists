<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use BlackMentalHealthVillage\Therapists\Console\Command\ReadmeCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(ReadmeCommand::class)]
final class ReadmeCommandTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

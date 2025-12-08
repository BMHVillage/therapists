<?php

declare(strict_types=1);

namespace Tests\Unit\Container;

use BlackMentalHealthVillage\Therapists\Container\TherapistsDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(TherapistsDefinition::class)]
final class TherapistsDefinitionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

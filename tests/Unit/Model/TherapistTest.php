<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use BlackMentalHealthVillage\Therapists\Model\Therapist;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(Therapist::class)]
final class TherapistTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

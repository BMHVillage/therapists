<?php

declare(strict_types=1);

namespace Tests\Unit\Container\Illuminate\Database\Capsule;

use BlackMentalHealthVillage\Therapists\Container\Illuminate\Database\Capsule\ManagerExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(ManagerExtension::class)]
final class ManagerExtensionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

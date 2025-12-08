<?php

declare(strict_types=1);

namespace Tests\Unit\Container\Illuminate\Database\Capsule;

use BlackMentalHealthVillage\Therapists\Container\Illuminate\Database\Capsule\ManagerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(ManagerFactory::class)]
final class ManagerFactoryTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Container\Illuminate\Events;

use BlackMentalHealthVillage\Therapists\Container\Illuminate\Events\DispatcherFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(DispatcherFactory::class)]
final class DispatcherFactoryTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

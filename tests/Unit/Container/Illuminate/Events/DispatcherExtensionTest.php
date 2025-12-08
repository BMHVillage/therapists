<?php

declare(strict_types=1);

namespace Tests\Unit\Container\Illuminate\Events;

use BlackMentalHealthVillage\Therapists\Container\Illuminate\Events\DispatcherExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(DispatcherExtension::class)]
final class DispatcherExtensionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}

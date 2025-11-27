<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Illuminate\Database\Capsule;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Override;
use Throwable;

/**
 * @see ManagerFactoryTest
 *
 * @implements FactoryInterface<Manager>
 */
final readonly class ManagerFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): Manager
    {
        return new Manager($container->get(Container::class));
    }
}

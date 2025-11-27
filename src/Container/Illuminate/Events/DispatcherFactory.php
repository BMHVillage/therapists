<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Illuminate\Events;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Events\Dispatcher;
use Override;
use Throwable;

/**
 * @see DispatcherFactoryTest
 *
 * @implements FactoryInterface<Dispatcher>
 */
final readonly class DispatcherFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): Dispatcher
    {
        return new Dispatcher($container->get(Container::class));
    }
}

<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Factory;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Override;
use Throwable;

/**
 * @see DispatcherFactoryTest
 *
 * @implements FactoryInterface<Dispatcher>
 */
final readonly class IlluminateEventsDispatcherFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): Dispatcher
    {
        $dispatcher = new Dispatcher($container->get(Container::class));

        Model::setEventDispatcher($dispatcher);

        return $dispatcher;
    }
}

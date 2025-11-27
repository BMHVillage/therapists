<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Illuminate\Events;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\ExtensionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Override;
use Throwable;

use function assert;

/**
 * @see DispatcherExtensionTest
 *
 * @implements ExtensionInterface<Dispatcher>
 */
final readonly class DispatcherExtension implements ExtensionInterface
{
    /**
     * @param Dispatcher $service
     *
     * @throws Throwable
     */
    #[Override]
    public function __invoke(ContainerInterface $container, object $service): void
    {
        assert($service instanceof \Illuminate\Contracts\Events\Dispatcher);

        Model::setEventDispatcher($service);
    }
}

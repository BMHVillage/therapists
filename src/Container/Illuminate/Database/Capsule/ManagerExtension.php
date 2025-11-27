<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Illuminate\Database\Capsule;

use Ghostwriter\Config\Interface\ConfigurationInterface;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\ExtensionInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager;
use Override;
use Throwable;

use function assert;

/**
 * @see ManagerExtensionTest
 *
 * @implements ExtensionInterface<Manager>
 */
final readonly class ManagerExtension implements ExtensionInterface
{
    /**
     * @param Manager $service
     *
     * @throws Throwable
     */
    #[Override]
    public function __invoke(ContainerInterface $container, object $service): void
    {
        assert($service instanceof Manager);

        $service->setEventDispatcher($container->get(Dispatcher::class));

        $configuration = $container->get(ConfigurationInterface::class);

        /** @var non-empty-string $defaultConnection */
        $defaultConnection = $configuration->get('database.default', 'sqlite');

        foreach ($configuration->get('database.connections', []) as $name => $connection) {
            if ($defaultConnection === $name) {
                $service->addConnection($connection);

                continue;
            }

            $service->addConnection($connection, $name);
        }

        $service->setAsGlobal();
        $service->bootEloquent();
    }
}

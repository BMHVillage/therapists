<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Factory;

use BlackMentalHealthVillage\Therapists\Configuration\TherapistsConfiguration;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcherInterface;
use Illuminate\Database\Capsule\Manager;
use Override;
use Throwable;

/**
 * @see IlluminateDatabaseCapsuleManagerFactoryTest
 *
 * @implements FactoryInterface<Manager>
 */
final readonly class IlluminateDatabaseCapsuleManagerFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): Manager
    {
        $manager = new Manager($container->get(Container::class));

        $manager->setEventDispatcher($container->get(IlluminateDispatcherInterface::class));

        $databaseConfiguration = $container->get(TherapistsConfiguration::class)->wrap('database');

        /** @var non-empty-string $defaultConnection */
        $defaultConnection = $databaseConfiguration->get('default', 'sqlite');

        foreach ($databaseConfiguration->get('connections', []) as $name => $connection) {
            if ($defaultConnection === $name) {
                $manager->addConnection($connection);

                continue;
            }

            $manager->addConnection($connection, $name);
        }

        $manager->setAsGlobal();
        $manager->bootEloquent();

        return $manager;
    }
}

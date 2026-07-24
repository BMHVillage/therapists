<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container;

use BlackMentalHealthVillage\Therapists\Configuration\TherapistsConfiguration;
use BlackMentalHealthVillage\Therapists\Container\Factory\ContainerCommandLoaderFactory;
use BlackMentalHealthVillage\Therapists\Container\Factory\IlluminateDatabaseCapsuleManagerFactory;
use BlackMentalHealthVillage\Therapists\Container\Factory\IlluminateEventsDispatcherFactory;
use BlackMentalHealthVillage\Therapists\Container\Factory\SymfonyApplicationFactory;
use BlackMentalHealthVillage\Therapists\Container\Factory\TherapistsConfigurationFactory;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Contracts\Container\Container as IlluminateContainerInterface;
use Illuminate\Contracts\Events\Dispatcher as IlluminateDispatcherInterface;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Override;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Throwable;

/**
 * @see TherapistsProviderTest
 */
final class TherapistsProvider extends AbstractProvider
{
    /**
     * [alias => service].
     *
     * @var array<class-string,class-string>
     */
    public const array ALIAS = [
        IlluminateContainerInterface::class => IlluminateContainer::class,
        IlluminateDispatcherInterface::class => IlluminateDispatcher::class,
    ];

    /**
     * [service => factory].
     *
     * @var array<class-string,class-string<FactoryInterface>>
     */
    public const array FACTORY = [
        Manager::class => IlluminateDatabaseCapsuleManagerFactory::class,
        IlluminateDispatcher::class => IlluminateEventsDispatcherFactory::class,
        TherapistsConfiguration::class => TherapistsConfigurationFactory::class,
        SymfonyApplication::class => SymfonyApplicationFactory::class,
        CommandLoaderInterface::class => ContainerCommandLoaderFactory::class,
    ];

    /** @throws Throwable */
    #[Override]
    public function boot(ContainerInterface $container): void
    {
        $container->get(Manager::class);
    }
}

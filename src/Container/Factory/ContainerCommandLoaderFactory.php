<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Factory;

use BlackMentalHealthVillage\Therapists\Configuration\TherapistsConfiguration;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Override;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Throwable;

use function is_array;

/**
 * @see ContainerCommandLoaderFactoryTest
 *
 * @implements FactoryInterface<ContainerCommandLoader>
 */
final readonly class ContainerCommandLoaderFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): ContainerCommandLoader
    {
        $configuration = $container->get(TherapistsConfiguration::class);

        /** @var ?array<non-empty-string,class-string<Command>> $commands */
        $commands = $configuration->get('console.commands', []);

        if (! is_array($commands)) {
            $commands = [];
        }

        return new ContainerCommandLoader($container->get(\Psr\Container\ContainerInterface::class), $commands);
    }
}

<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Factory;

use BlackMentalHealthVillage\Therapists\Configuration\TherapistsConfiguration;
use Composer\InstalledVersions;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Override;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Throwable;

use function is_string;

/**
 * @see SymfonyApplicationFactoryTest
 *
 * @implements FactoryInterface<Application>
 */
final readonly class SymfonyApplicationFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): Application
    {
        $configuration = $container->get(TherapistsConfiguration::class);

        $consoleConfiguration = $configuration->wrap('console');

        $symfonyApplication = new Application(
            $consoleConfiguration->get('name'),
            InstalledVersions::getPrettyVersion($consoleConfiguration->get('package'))
        );

        $symfonyApplication->setAutoExit($consoleConfiguration->get('auto_exit', false));
        $symfonyApplication->setCatchErrors($consoleConfiguration->get('catch_errors', false));
        $symfonyApplication->setCatchExceptions($consoleConfiguration->get('catch_exceptions', false));
        $symfonyApplication->setCommandLoader($container->get(CommandLoaderInterface::class));

        $defaultCommand = $consoleConfiguration->get('default_command', false);
        if (! is_string($defaultCommand)) {
            return $symfonyApplication;
        }

        $singleCommand = $consoleConfiguration->get('single_command', false);
        $symfonyApplication->setDefaultCommand($defaultCommand, true === $singleCommand);

        return $symfonyApplication;
    }
}

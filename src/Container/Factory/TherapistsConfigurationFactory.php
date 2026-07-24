<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Container\Factory;

use BlackMentalHealthVillage\Therapists\Configuration\TherapistsConfiguration;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Override;
use Throwable;

use function base_path;

/**
 * @see TherapistsConfigurationFactoryTest
 *
 * @implements FactoryInterface<TherapistsConfiguration>
 */
final readonly class TherapistsConfigurationFactory implements FactoryInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): TherapistsConfiguration
    {
        $configuration = TherapistsConfiguration::new();

        $configuration->mergeDirectory(base_path('config'));

        return $configuration;
    }
}

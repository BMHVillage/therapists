<?php

declare(strict_types=1);

use BlackMentalHealthVillage\Therapists\Container\Illuminate\Database\Capsule\ManagerExtension;
use BlackMentalHealthVillage\Therapists\Container\Illuminate\Database\Capsule\ManagerFactory;
use BlackMentalHealthVillage\Therapists\Container\Illuminate\Events\DispatcherExtension;
use BlackMentalHealthVillage\Therapists\Container\Illuminate\Events\DispatcherFactory;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerInterface;
use Illuminate\Contracts\Events\Dispatcher as DispatcherInterface;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

return [
    'alias'=> [
        ContainerInterface::class => Container::class,
        DispatcherInterface::class => Dispatcher::class,
    ],
    'define'=> [],
    'extend'=> [
        Dispatcher::class => [DispatcherExtension::class],
        Manager::class => [ManagerExtension::class],
    ],
    'factory'=> [
        Manager::class => ManagerFactory::class,
        Dispatcher::class => DispatcherFactory::class,
    ],
];

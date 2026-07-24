<?php

declare(strict_types=1);

namespace BlackMentalHealthVillage\Therapists\Configuration;

use Ghostwriter\Config\AbstractConfiguration;

/**
 * @see TherapistsConfigurationTest
 *
 * @extends AbstractConfiguration<array{
 *     app: array{debug: bool},
 *     console: array{name:string,package:string,commands:array<string,class-string>,auto_exit:bool,catch_errors:bool,catch_exceptions:bool,default_command:bool|class-string,single_command:bool},
 *     database: array{default: string, connections: array<string, array{driver: string, host: string, port: int, database: string, username: string, password: string, charset: string, collation: string, prefix: string, prefix_indexes: bool, strict: bool, engine: null|string, options: array<int|string, mixed>}>, migrations: string},
 *     logging: array{default: string, channels: array<string, array{driver: string, path?: null|string, level?: null|string}>}
 * }>
 */
final class TherapistsConfiguration extends AbstractConfiguration {}

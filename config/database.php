<?php

declare(strict_types=1);

return [
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => \dotenv('DB_URL'),
            'database' => \dotenv('DB_DATABASE', \database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => true,
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => \dotenv('DB_URL'),
            'host' => \dotenv('DB_HOST', '127.0.0.1'),
            'port' => \dotenv('DB_PORT', '5432'),
            'database' => \dotenv('DB_DATABASE', 'therapists'),
            'username' => \dotenv('DB_USERNAME', 'bmhv'),
            'password' => \dotenv('DB_PASSWORD', 'secret'),
            'charset' => \dotenv('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
];

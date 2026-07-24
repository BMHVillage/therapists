<?php

declare(strict_types=1);
return [
    'default' => 'stack',
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['file'],
        ],
        'file' => [
            'driver' => 'file',
            'path' => \storage_path('logs', 'therapists.log'),
            'level' => \dotenv('LOG_LEVEL', 'debug'),
        ],
    ],
];

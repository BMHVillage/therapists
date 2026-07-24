<?php

declare(strict_types=1);

/**
 * @return array{debug:bool}
 */
return [
    'debug' => \dotenv('APP_DEBUG', false),
];

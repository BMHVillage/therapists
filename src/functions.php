<?php

declare(strict_types=1);

if (! \function_exists('base_path')) {
    function base_path(string ...$segments): string
    {
        return \implode(\DIRECTORY_SEPARATOR, [WORKSPACE_PATH, ...$segments]);
    }
}
if (! \function_exists('dotenv')) {
    function dotenv(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;

        if (null === $value) {
            return $default;
        }

        return match (\mb_strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }

}

if (! \function_exists('database_path')) {
    function database_path(string ...$segments): string
    {
        return \base_path('database', ...$segments);
    }
}

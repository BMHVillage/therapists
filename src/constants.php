<?php

declare(strict_types=1);

if (! \defined('WORKSPACE_PATH')) {
    \define(
        'WORKSPACE_PATH',
        \getcwd() ?: throw new \RuntimeException('Cannot determine the current working directory.')
    );
}

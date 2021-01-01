<?php

use PhpCaddy\Commands;

/**
 * The default set of registered commands.
 */
return [
    "commands" => [
        Commands\Run::class,
        Commands\ListModules::class,
    ],
];

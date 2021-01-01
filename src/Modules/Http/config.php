<?php

namespace PhpCaddy\Modules\Http;

use PhpCaddy\Core;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

return [
    LoopInterface::class => fn() => Factory::create(),
];

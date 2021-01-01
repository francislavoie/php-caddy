<?php

namespace PhpCaddy;

use Symfony\Component\Console\Application;

class Console
{
    /** @var Application */
    private Application $app;

    /**
     * Console constructor.
     *
     * @throws
     */
    public function __construct()
    {
        $this->app = new Application();

        $container = Core::container();
        foreach ($container->get('commands') as $command) {
            $this->app->add(new $command);
        }
    }

    /**
     * Entrypoint for the console application.
     *
     * @throws
     */
    public function run()
    {
        $this->app->run();
    }
}

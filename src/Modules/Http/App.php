<?php

namespace PhpCaddy\Modules\Http;

use PhpCaddy\Module;
use PhpCaddy\ModuleInfo;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Message\Response;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

class App implements Module
{
    /**
     * @var LoopInterface
     */
    private LoopInterface $loop;

    public static function module() : ModuleInfo
    {
        return new ModuleInfo("http");
    }

    /**
     * App constructor.
     *
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function start() : void
    {
        $socket = new SocketServer(8080, $this->loop);
        $server = new HttpServer($this->loop, [self::class, 'handleRequest']);
        $server->listen($socket);

        $this->loop->run();
    }

    /**
     * Handle the HTTP request based on configuration.
     *
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function handleRequest(ServerRequestInterface $request) : Response
    {
        return new Response(200, ['Content-Type' => 'text/plain'], "Hello World!\n");
    }

    public function stop() : void
    {
        $this->loop->stop();
    }
}

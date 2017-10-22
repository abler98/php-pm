<?php

namespace PHPPM\Bridges;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use RingCentral\Psr7;
use React\EventLoop\LoopInterface;

class Psr15Bridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($appBootstrap, $appenv, $debug, LoopInterface $loop)
    {
        parent::bootstrap($appBootstrap, $appenv, $debug, $loop);

        if (!$this->middleware instanceof RequestHandlerInterface) {
            throw new \Exception('Middleware must implement RequestHandlerInterface');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request)
    {
        if ($this->middleware === null) {
            // internal server error
            return new Psr7\Response(500, ['Content-type' => 'text/plain'], 'Middleware not configured during bootstrap');
        }

        $middleware = $this->middleware;
        return $middleware->handle($request);
    }
}

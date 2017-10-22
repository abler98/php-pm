<?php

namespace PHPPM\Bridges;

use PHPPM\Bootstraps\ApplicationEnvironmentAwareInterface;
use PHPPM\Bootstraps\AsyncInterface;
use React\EventLoop\LoopInterface;

abstract class AbstractBridge implements BridgeInterface
{
    protected $middleware;

    /**
     * Bootstrap an application implementing the PSR-15 RequestHandler interface.
     *
     * @param string $appBootstrap The name of the class used to bootstrap the application
     * @param string|null $appenv The environment your application will use to bootstrap (if any)
     * @param boolean $debug If debug is enabled
     * @param LoopInterface $loop The event loop
     */
    public function bootstrap($appBootstrap, $appenv, $debug, LoopInterface $loop)
    {
        $appBootstrap = $this->normalizeAppBootstrap($appBootstrap);

        $this->middleware = new $appBootstrap;
        if ($this->middleware instanceof ApplicationEnvironmentAwareInterface) {
            $this->middleware->initialize($appenv, $debug);
        }
        if ($this->middleware instanceof AsyncInterface) {
            $this->middleware->setLoop($loop);
        }
    }

    /**
     * @param $appBootstrap
     * @return string
     * @throws \RuntimeException
     */
    protected function normalizeAppBootstrap($appBootstrap)
    {
        $appBootstrap = str_replace('\\\\', '\\', $appBootstrap);

        $bootstraps = [
            $appBootstrap,
            '\\' . $appBootstrap,
            '\\PHPPM\Bootstraps\\' . ucfirst($appBootstrap)
        ];

        foreach ($bootstraps as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return $appBootstrap;
    }
}

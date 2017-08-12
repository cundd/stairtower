<?php
declare(strict_types=1);

use Evenement\EventEmitter;
use Evenement\EventEmitterInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use function DI\object;

return call_user_func(
    function () {
        $ns = 'Cundd\\Stairtower\\';

        return [
            $ns . 'Formatter\\FormatterInterface'               => object($ns . 'Formatter\\Formatter'),
            $ns . 'Server\\Handler\\HandlerInterface'           => object($ns . 'Server\\Handler\\Handler'),
            $ns . 'Server\\ServerInterface'                     => object($ns . 'Server\\RestServer'),
            $ns . 'Server\\UriBuilderInterface'                 => object($ns . 'Server\\UriBuilder'),
            $ns . 'Server\\Cookie\\CookieParserInterface'       => object($ns . 'Server\\Cookie\\CookieParser'),
            $ns . 'Server\\Session\\SessionProviderInterface'   => object($ns . 'Server\\Session\\SessionProvider'),
            $ns . 'Server\\Dispatcher\\CoreDispatcherInterface' => object($ns . 'Server\\Dispatcher\\CoreDispatcher'),
            $ns . 'Server\\Dispatcher\\CoreDispatcher'          => object($ns . 'Server\\Dispatcher\\CoreDispatcher'),
            $ns . 'Asset\\AssetProviderInterface'               => object($ns . 'Asset\\AssetProvider'),
            $ns . 'DataAccess\\*Interface'                      => object($ns . 'DataAccess\\*'),
            $ns . 'Serializer\\SerializerInterface'             => object($ns . 'Serializer\\JsonSerializer'),
            $ns . 'Filter\\FilterBuilderInterface'              => object($ns . 'Filter\\FilterBuilder'),
            $ns . 'Expand\\ExpandConfigurationBuilderInterface' => object($ns . 'Expand\\ExpandConfigurationBuilder'),
            $ns . 'Expand\\ExpandResolverInterface'             => object($ns . 'Expand\\ExpandResolver'),
            EventEmitterInterface::class                        => object(EventEmitter::class),

            LoopInterface::class => DI\factory(
                function () {
                    return Factory::create();
                }
            ),
        ];
    }
);

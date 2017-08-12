<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

trait RequestBuilderTrait
{
    /**
     * @param string $method
     * @param string $path
     * @param array  $query
     * @return ServerRequestInterface
     */
    public static function buildRequest(string $method, string $path, array $query = [])
    {
        $prophet = new Prophet();

        return static::buildRequestWithProphecy(
            $prophet->prophesize(ServerRequestInterface::class),
            $method,
            $path,
            $query
        );
    }

    /**
     * @param ObjectProphecy|ServerRequestInterface $prophecy
     * @param string                                $method
     * @param string                                $path
     * @param array                                 $query
     * @return ServerRequestInterface
     */
    public static function buildRequestWithProphecy(
        ObjectProphecy $prophecy,
        string $method,
        string $path,
        array $query = []
    ) {
        $prophet = new Prophet();

//        'GET', '/contacts/', $query
        $prophecy->getQueryParams()->willReturn($query);

        /** @var ObjectProphecy|UriInterface $uri */
        $uri = $prophet->prophesize(UriInterface::class);
        $uri->getPath()->willReturn($path);

        $prophecy->getUri()->willReturn($uri->reveal());
        $prophecy->getMethod()->willReturn($method);

        $prophecy->getBody()->willReturn(null);
        $prophecy->getParsedBody()->willReturn(null);

        return $prophecy->reveal();
    }
}
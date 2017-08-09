<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Fixtures;

use Cundd\Stairtower\Server\Session\SessionControllerTrait;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;

class TestSessionController
{
    use SessionControllerTrait;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @return RequestInterface
     */
    public function getRequest():?RequestInterface
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}

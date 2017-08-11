<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Fixtures;

use Cundd\Stairtower\Server\Controller\ControllerInterface;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;
use Cundd\Stairtower\Server\ValueObject\ControllerResult;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;


class TestApplicationController implements ControllerInterface
{
    public function initialize(): void
    {
    }

    public function setRequest(RequestInterface $request): ControllerInterface
    {
        return $this;
    }

    public function getRequest(): RequestInterface
    {
        return null;
    }

    public function unsetRequest(): void
    {
    }

    public function willInvokeAction(string $action): void
    {
    }

    public function didInvokeAction(string $action, ControllerResultInterface $result): void
    {

    }

    public function getMyMethodAction()
    {
    }

    public function postMyMethodAction()
    {
    }

    public function deleteMyMethodAction()
    {
    }

    public function putMyMethodAction()
    {
    }

    public function headMyMethodAction()
    {
    }

    public function processRequest(
        RequestInterface $request
    ): ControllerResultInterface {
        return new ControllerResult(0);
    }
}

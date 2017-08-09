<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\System\Lock;


use Cundd\Stairtower\System\Lock\Factory;
use Cundd\Stairtower\System\Lock\FileLock;
use Cundd\Stairtower\System\Lock\TransientLock;
use Cundd\Stairtower\Tests\Unit\AbstractCase;


/**
 * Class FactoryTest
 */
class FactoryTest extends AbstractCase
{
    /**
     * @test
     */
    public function getLockTest()
    {
        $this->assertInstanceOf(FileLock::class, Factory::createLock('test'));

        Factory::setLockImplementationClass(TransientLock::class);
        $this->assertInstanceOf(
            TransientLock::class,
            Factory::createLock('test')
        );
    }
}
 
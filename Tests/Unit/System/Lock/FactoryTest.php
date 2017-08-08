<?php
declare(strict_types=1);

namespace Cundd\Stairtower\System\Lock;


use Cundd\Stairtower\AbstractCase;


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
 
<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\System\Lock;


use Cundd\PersistentObjectStore\AbstractCase;


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
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\System\\Lock\\FileLock', Factory::createLock('test'));

        Factory::setLockImplementationClass('Cundd\\PersistentObjectStore\\System\\Lock\\TransientLock');
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\System\\Lock\\TransientLock',
            Factory::createLock('test')
        );

    }
}
 
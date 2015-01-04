<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:42
 */

namespace Cundd\PersistentObjectStore\System\Lock;


use Cundd\PersistentObjectStore\AbstractCase;


/**
 * Class FactoryTest
 *
 * @package Cundd\PersistentObjectStore\System\Lock
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
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\System\\Lock\\TransientLock',
            Factory::createLock('test'));

    }
}
 
<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\System\Lock;


use Cundd\Stairtower\System\Lock\LockInterface;
use Cundd\Stairtower\System\Lock\TransientLock;
use Cundd\Stairtower\Tests\Unit\AbstractCase;


/**
 * Class TransientLockTest
 */
class TransientLockTest extends AbstractCase
{
    /**
     * @var LockInterface
     */
    protected $fixture;

    /**
     * @test
     */
    public function lockTest()
    {
        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());
    }

    /**
     * @test
     */
    public function unlockTest()
    {
        $this->fixture = new TransientLock();

        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());

        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());

        unset($this->fixture);
    }

    /**
     * @test
     */
    public function tryLockTest()
    {
        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->tryLock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());
    }

    /**
     * @test
     */
    public function namedLockTest()
    {
        $this->fixture = new TransientLock('lock-name/with-a-slash');

        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());
    }

    /**
     * @test
     */
    public function namedUnlockTest()
    {
        $this->fixture = new TransientLock('lock-name/with-a-slash');

        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());

        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());

        unset($this->fixture);
    }

    /**
     * @test
     */
    public function namedTryLockTest()
    {
        $this->fixture = new TransientLock('lock-name/with-a-slash');

        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->tryLock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());
    }

    protected function tearDown()
    {
        unset($this->fixture);

        parent::tearDown();
    }
}
 
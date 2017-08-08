<?php
declare(strict_types=1);

namespace Cundd\Stairtower\System\Lock;


use Cundd\Stairtower\AbstractCase;


class FileLockWithAccessToFilePath extends FileLock
{
    /**
     * Returns the path to the lock file.
     *
     * @return    string
     */
    public function getProtectedLockPath()
    {
        return $this->getLockPath();
    }
}

/**
 * Class FileLockTest
 */
class FileLockTest extends AbstractCase
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
        $this->fixture = new FileLockWithAccessToFilePath();
        $lockPath = $this->fixture->getProtectedLockPath();

        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());
        $this->assertFileNotExists($lockPath);

        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->assertFileExists($lockPath);

        unset($this->fixture);
        $this->assertFileNotExists($lockPath);
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
        $this->fixture = new FileLockWithAccessToFilePath('lock-name/with-a-slash');

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
        $this->fixture = new FileLockWithAccessToFilePath('lock-name/with-a-slash');
        $lockPath = $this->fixture->getProtectedLockPath();

        $this->assertFalse($this->fixture->isLocked());
        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->fixture->unlock();
        $this->assertFalse($this->fixture->isLocked());
        $this->assertFileNotExists($lockPath);

        $this->fixture->lock();
        $this->assertTrue($this->fixture->isLocked());
        $this->assertFileExists($lockPath);

        unset($this->fixture);
        $this->assertFileNotExists($lockPath);
    }

    /**
     * @test
     */
    public function namedTryLockTest()
    {
        $this->fixture = new FileLockWithAccessToFilePath('lock-name/with-a-slash');

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
 
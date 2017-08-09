<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

use Cundd\Stairtower\Server\Controller\AbstractController;
use PHPUnit\Framework\TestCase;

class ClassBuilderTraitTest extends TestCase
{
    /**
     * @test
     */
    public function buildClassTest()
    {
        $className = 'MyClass' . bin2hex(random_bytes(4));
        $this->assertTrue(
            ClassBuilderTrait::buildClass($className),
            "Class $className could not be created"
        );
        $this->assertTrue(class_exists($className));
    }

    /**
     * @test
     */
    public function buildClassExtendingTest()
    {
        $className = 'MyClass' . bin2hex(random_bytes(4));

        $this->assertTrue(
            ClassBuilderTrait::buildClass($className, AbstractController::class),
            "Class $className could not be created"
        );
        $this->assertTrue(class_exists($className));
        $this->assertTrue(
            is_a($className, AbstractController::class, true),
            sprintf('Class %s does not extend %s', $className, AbstractController::class)
        );
    }

    /**
     * @test
     */
    public function buildClassWithNamespaceTest()
    {
        $className = '\\MyVendor\\MyNamespace\\MyClass' . bin2hex(random_bytes(4));
        $this->assertTrue(
            ClassBuilderTrait::buildClass($className),
            "Class $className could not be created"
        );
        $this->assertTrue(class_exists($className));
    }

    /**
     * @test
     */
    public function buildClassWithNamespaceExtendingTest()
    {
        $className = '\\MyVendor\\MyNamespace\\MyClass' . bin2hex(random_bytes(4));

        $this->assertTrue(
            ClassBuilderTrait::buildClass($className, AbstractController::class),
            "Class $className could not be created"
        );
        $this->assertTrue(class_exists($className));
        $this->assertTrue(
            is_a($className, AbstractController::class, true),
            sprintf('Class %s does not extend %s', $className, AbstractController::class)
        );
    }
}

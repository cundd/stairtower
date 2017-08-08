<?php
declare(strict_types=1);

namespace Cundd\Stairtower\View\Twig;

use Cundd\Stairtower\View\ViewInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Twig based View
 */
class ViewTest extends TestCase
{
    /**
     * @var ViewInterface
     */
    protected $fixture;

    protected function setUp()
    {
        parent::setUp();
        if (class_exists('Twig_Environment')) {
            $this->fixture = new View();
        }
    }

    protected function tearDown()
    {
        unset($this->fixture);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function simpleTest()
    {
        $this->skipTestsIfTwigIsNotInstalled();
        $this->fixture->setTemplatePath(__DIR__ . '/../../../Resources/Templates/simple.twig');
        $this->assertEquals('<h1>This is a very simple template</h1>', $this->fixture->render());
    }

    /**
     * @test
     */
    public function variablesTest()
    {
        $this->skipTestsIfTwigIsNotInstalled();
        $this->fixture->setTemplatePath(__DIR__ . '/../../../Resources/Templates/variables.twig');
        $this->fixture->assign('view', 'view');
        $this->assertEquals('<h1>This is a view template with variables</h1>', $this->fixture->render());
    }

    /**
     * @test
     */
    public function missingVariableTest()
    {
        $this->skipTestsIfTwigIsNotInstalled();
        $this->fixture->setTemplatePath(__DIR__ . '/../../../Resources/Templates/missing-variable.twig');
        $this->fixture->assign('view', 'view');
        $this->assertEquals('<h1>This is a view template with a "" variables</h1>', $this->fixture->render());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\View\Exception\InvalidTemplatePathException
     */
    public function noTemplatePathExceptionTest()
    {
        $this->skipTestsIfTwigIsNotInstalled();
        $this->fixture->render();
    }

    private function skipTestsIfTwigIsNotInstalled()
    {
        if (!$this->fixture) {
            $this->markTestSkipped('View engine not available');
        }
    }
}

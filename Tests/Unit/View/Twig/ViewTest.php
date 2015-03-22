<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 22.03.15
 * Time: 12:00
 */

namespace Cundd\PersistentObjectStore\View\Twig;


use Cundd\PersistentObjectStore\View\ViewInterface;

/**
 * Tests for the Twig based View
 *
 * @package Cundd\PersistentObjectStore\View\Twig
 */
class ViewTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ViewInterface
     */
    protected $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new View();
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
        $this->fixture->setTemplatePath(__DIR__ . '/../../../Resources/Templates/simple.twig');
        $this->assertEquals('<h1>This is a very simple template</h1>', $this->fixture->render());
    }

    /**
     * @test
     */
    public function variablesTest()
    {
        $this->fixture->setTemplatePath(__DIR__ . '/../../../Resources/Templates/variables.twig');
        $this->fixture->assign('view', 'view');
        $this->assertEquals('<h1>This is a view template with variables</h1>', $this->fixture->render());
    }

    /**
     * @test
     */
    public function missingVariableTest()
    {
        $this->fixture->setTemplatePath(__DIR__ . '/../../../Resources/Templates/missing-variable.twig');
        $this->fixture->assign('view', 'view');
        $this->assertEquals('<h1>This is a view template with a "" variables</h1>', $this->fixture->render());
    }
}

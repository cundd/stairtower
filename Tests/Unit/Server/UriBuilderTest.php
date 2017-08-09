<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Server\UriBuilderInterface;
use Cundd\Stairtower\Tests\Fixtures\TestApplicationController;
use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;
use Cundd\Stairtower\Tests\Unit\ClassBuilderTrait;

/**
 * Tests for creating URIs
 */
class UriBuilderTest extends AbstractDatabaseBasedCase
{
    /**
     * @var UriBuilderInterface
     */
    protected $fixture;

    protected function makeInstance($className)
    {
        if (!ctype_alnum(str_replace(['\\', '_'], '', $className))) {
            die;
        }

        ClassBuilderTrait::buildClass($className, TestApplicationController::class);

        return new $className;
    }

    /**
     * @test
     */
    public function buildUriTests()
    {
        $document = new Document();
        $document->setValueForKey('0b5e3637477c', Constants::DATA_ID_KEY);
        $database = $this->getSmallPeopleDatabase();

        $this->assertEquals(
            '/_cundd-sa-hello/blur',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Sa\\Controller\\HelloController'
            )
        );
        $this->assertEquals(
            '/_cundd-sa-hello/blur/database',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Sa\\Controller\\HelloController',
                'database'
            )
        );
        $this->assertEquals(
            '/_cundd-sa-hello/blur/people-small',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Sa\\Controller\\HelloController',
                $database
            )
        );
        $this->assertEquals(
            '/_cundd-sa-hello/blur/database/document-id',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Sa\\Controller\\HelloController',
                'database',
                'document-id'
            )
        );
        $this->assertEquals(
            '/_cundd-sa-hello/blur/database/0b5e3637477c',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Sa\\Controller\\HelloController',
                'database',
                $document
            )
        );
        $this->assertEquals(
            '/_cundd-sa-hello/blur/people-small/0b5e3637477c',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Sa\\Controller\\HelloController',
                $database,
                $document
            )
        );
        $this->assertEquals(
            '/_cundd-sa-hello/blur',
            $this->fixture->buildUriFor(
                'blur',
                $this->makeInstance('Cundd\\Sa\\Controller\\HelloController')
            )
        );


        $this->assertEquals(
            '/_cundd-stair-application/blur',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Stair\\Controller\\ApplicationController'
            )
        );
        $this->assertEquals(
            '/_cundd-stair-application/blur/database',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Stair\\Controller\\ApplicationController',
                'database'
            )
        );
        $this->assertEquals(
            '/_cundd-stair-application/blur/people-small',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Stair\\Controller\\ApplicationController',
                $database
            )
        );
        $this->assertEquals(
            '/_cundd-stair-application/blur/database/document-id',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Stair\\Controller\\ApplicationController',
                'database',
                'document-id'
            )
        );
        $this->assertEquals(
            '/_cundd-stair-application/blur/database/0b5e3637477c',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Stair\\Controller\\ApplicationController',
                'database',
                $document
            )
        );
        $this->assertEquals(
            '/_cundd-stair-application/blur/people-small/0b5e3637477c',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\Stair\\Controller\\ApplicationController',
                $database,
                $document
            )
        );
        $this->assertEquals(
            '/_cundd-stair-application/blur',
            $this->fixture->buildUriFor(
                'blur',
                $this->makeInstance('Cundd\\Stair\\Controller\\ApplicationController')
            )
        );


        $this->assertEquals(
            '/_cundd-stair_way-application/blur',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController'
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/database',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                'database'
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/people-small',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                $database
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/database/document-id',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                'database',
                'document-id'
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/database/0b5e3637477c',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                'database',
                $document
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/people-small/0b5e3637477c',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                $database,
                $document
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur',
            $this->fixture->buildUriFor(
                'blur',
                $this->makeInstance('Cundd\\StairWay\\Controller\\ApplicationController')
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/people-small/0b5e3637477c?a=1&b=2',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                $database,
                $document,
                ['a' => 1, 'b' => 2]
            )
        );
        $this->assertEquals(
            '/_cundd-stair_way-application/blur/people-small/0b5e3637477c?a=1&b=2#a/uri/fragment',
            $this->fixture->buildUriFor(
                'blur',
                'Cundd\\StairWay\\Controller\\ApplicationController',
                $database,
                $document,
                ['a' => 1, 'b' => 2],
                'a/uri/fragment'
            )
        );
    }

    /**
     * @test
     */
    public function getControllerNamespaceForControllerTests()
    {
        $this->assertEquals(
            '_cundd-sa-hello',
            $this->fixture->getControllerNamespaceForController(
                'Cundd\\Sa\\Controller\\HelloController'
            )
        );
        $this->assertEquals(
            '_cundd-sa-hello',
            $this->fixture->getControllerNamespaceForController(
                $this->makeInstance('Cundd\\Sa\\Controller\\HelloController')
            )
        );

        $this->assertEquals(
            '_cundd-stair-application',
            $this->fixture->getControllerNamespaceForController(
                'Cundd\\Stair\\Controller\\ApplicationController'
            )
        );
        $this->assertEquals(
            '_cundd-stair-application',
            $this->fixture->getControllerNamespaceForController(
                $this->makeInstance('Cundd\\Stair\\Controller\\ApplicationController')
            )
        );

        $this->assertEquals(
            '_cundd-stair_way-application',
            $this->fixture->getControllerNamespaceForController(
                'Cundd\\StairWay\\Controller\\ApplicationController'
            )
        );
        $this->assertEquals(
            '_cundd-stair_way-application',
            $this->fixture->getControllerNamespaceForController(
                $this->makeInstance('Cundd\\StairWay\\Controller\\ApplicationController')
            )
        );
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException
     * @expectedExceptionCode 1422475362
     */
    public function noActionNameTest()
    {
        $this->fixture->buildUriFor('', 'HelloController', 'database', 'document-id');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException
     * @expectedExceptionCode 1422475419
     */
    public function noControllerTest()
    {
        $this->fixture->buildUriFor('blur', '', 'database', 'document-id');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException
     * @expectedExceptionCode 1422472522
     */
    public function invalidActionNameTest()
    {
        $this->fixture->buildUriFor(new \stdClass(), 'HelloController', 'database', 'document-id');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException
     * @expectedExceptionCode 1422472579
     */
    public function invalidDatabaseTest()
    {
        $this->fixture->buildUriFor('blur', 'HelloController', new \stdClass(), 'document-id');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException
     * @expectedExceptionCode 1422472633
     */
    public function invalidDocumentTest()
    {
        $this->fixture->buildUriFor('blur', 'HelloController', 'database', new \stdClass());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException
     * @expectedExceptionCode 1422472650
     */
    public function noControllerForGetControllerNamespaceForControllerTests()
    {
        $this->fixture->getControllerNamespaceForController('');
    }

}
 
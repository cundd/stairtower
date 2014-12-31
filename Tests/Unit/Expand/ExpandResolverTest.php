<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 18:33
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Domain\Model\Document;

/**
 * Dummy for Expand Resolver
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandResolver_withInjectableCoordinator extends ExpandResolver
{
    /**
     * Sets the Document Access Coordinator
     *
     * @param \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface $coordinator
     * @return $this
     */
    public function setCoordinator($coordinator)
    {
        $this->coordinator = $coordinator;
        return $this;
    }
}

/**
 * Expand Resolver test
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandResolverTest extends AbstractDatabaseBasedCase
{
    /**
     * @var ExpandResolver_withInjectableCoordinator
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = $this->getDiContainer()->get('Cundd\\PersistentObjectStore\\Expand\\ExpandResolver_withInjectableCoordinator');

        $coordinator = $this->getMockBuilder('Cundd\\PersistentObjectStore\\DataAccess\\CoordinatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $coordinator->expects($this->any())
            ->method('getDatabase')
            ->will($this->returnValue($this->getSmallPeopleDatabase()));

        $this->fixture->setCoordinator($coordinator);
    }

    /**
     * @test
     */
    public function expandDocumentValidTest()
    {
        $document      = new Document([
            'person' => 'spm@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person', 'people-small', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertEquals('spm@cundd.net', $document->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function expandDocumentWithNotExistingSearchValueTest()
    {
        $document      = new Document([
            'person' => time() . 'not-existing-email@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person', 'people-small', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));
    }

    /**
     * @test
     */
    public function expandDocumentWithNotExistingLocalKeyTest()
    {
        $document      = new Document([
            'person' => 'spm@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('not-existing-key', 'people-small', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('not-existing-key'));
        $this->assertEquals('spm@cundd.net', $document->valueForKeyPath('person'),
            'The property \'person\' should not have been touched');
    }

    /**
     * @test
     */
    public function expandDocumentWithNotExistingForeignKeyTest()
    {
        $document      = new Document([
            'person' => 'spm@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person', 'people-small', 'not-existing-key');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));
    }

    /**
     * @test
     */
    public function expandDocumentWithoutAnyValueTest()
    {
        $document      = new Document();
        $configuration = new ExpandConfiguration('person', 'people-small', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));
    }
}

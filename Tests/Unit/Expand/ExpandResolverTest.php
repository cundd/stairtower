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
    public function expandDocumentTest()
    {
        $document      = new Document([
            'person' => 'spm@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person-small', 'person', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertEquals('spm@cundd.net', $document->valueForKeyPath('person.email'));


        $document      = new Document([
            'person' => time() . 'not-existing-email@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person-small', 'person', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));


        $document      = new Document([
            'person' => 'spm@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person-small', 'not-existing-key', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));


        $document      = new Document([
            'person' => 'spm@cundd.net'
        ]);
        $configuration = new ExpandConfiguration('person-small', 'person', 'not-existing-key');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));


        $document      = new Document();
        $configuration = new ExpandConfiguration('person-small', 'person', 'email');
        $this->fixture->expandDocument($document, $configuration);
        $this->assertNull($document->valueForKeyPath('person'));
    }
}

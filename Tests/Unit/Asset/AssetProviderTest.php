<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.04.15
 * Time: 20:15
 */

namespace Cundd\PersistentObjectStore\Asset;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;

/**
 * Test for Asset Provider
 *
 * @package Cundd\PersistentObjectStore\Asset
 */
class AssetProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssetProviderInterface
     */
    protected $fixture;

    /**
     * @var string
     */
    protected static $publicResourcesPath = '';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $configurationManager = ConfigurationManager::getSharedInstance();
        self::$publicResourcesPath = $configurationManager->getConfigurationForKeyPath('publicResources');
        $configurationManager->setConfigurationForKeyPath('publicResources', __DIR__ . '/../../Resources/');
    }

    protected function setUp()
    {
        $this->fixture = new AssetProvider();
    }

    public static function tearDownAfterClass()
    {
        ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath(
            'publicResources',
            self::$publicResourcesPath
        );
        parent::tearDownAfterClass();
    }


    /**
     * @test
     */
    public function getAssetForUriTest()
    {
        $uri = 'book.json';
        $asset = $this->fixture->getAssetForUri($uri);
        $this->assertNotNull($asset);
        $this->assertEquals($uri, $asset->getUri());
        $this->assertContains('Beltz', $asset->getContent());
    }

    /**
     * @test
     */
    public function doNotGetAssetForUriTest()
    {
        $uri = 'this/does/not/exist.jpg';
        $this->assertNull($this->fixture->getAssetForUri($uri), sprintf('URI "%s" should not exist', $uri));
    }


    /**
     * @test
     */
    public function hasAssetForUriTest()
    {
        $this->assertTrue($this->fixture->hasAssetForUri('book.json'));
    }

    /**
     * @test
     */
    public function doNotHasAssetForUriTest()
    {
        $uri = 'this/does/not/exist.jpg';
        $this->assertFalse($this->fixture->hasAssetForUri($uri), sprintf('URI "%s" should not exist', $uri));
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Asset\Exception\InvalidUriException
     * @expectedExceptionCode 1428518305
     */
    public function noUriTest()
    {
        $this->fixture->getAssetForUri('');
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Asset\Exception\InvalidUriException
     * @expectedExceptionCode 1428518310
     */
    public function uriContainsIllegalCharacterTest()
    {
        $this->fixture->getAssetForUri('/some/../path');
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Asset\Exception\InvalidUriException
     * @expectedExceptionCode 1428518315
     */
    public function uriIsNoStringTest()
    {
        $this->fixture->getAssetForUri([]);
    }

}

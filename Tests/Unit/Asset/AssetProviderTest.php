<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Asset;

use Cundd\Stairtower\Configuration\ConfigurationManager;

/**
 * Test for Asset Provider
 */
class AssetProviderTest extends \PHPUnit\Framework\TestCase
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
     * @expectedException \Cundd\Stairtower\Asset\Exception\InvalidUriException
     * @expectedExceptionCode 1428518305
     */
    public function noUriTest()
    {
        $this->fixture->getAssetForUri('');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Asset\Exception\InvalidUriException
     * @expectedExceptionCode 1428518310
     */
    public function uriContainsIllegalCharacterTest()
    {
        $this->fixture->getAssetForUri('/some/../path');
    }
}

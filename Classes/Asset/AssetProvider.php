<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Asset;

use Cundd\Stairtower\Asset\Exception\InvalidUriException;
use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Memory\Manager;

/**
 * Asset Provider implementation
 */
class AssetProvider implements AssetProviderInterface
{
    /**
     * Prefix for stored keys
     */
    const MEMORY_MANAGER_KEY_PREFIX = 'asset_';

    /**
     * Tag for session objects
     */
    const MEMORY_MANAGER_TAG = 'asset';

    public function hasAssetForUri(string $uri): bool
    {
        $this->assertUri($uri);

        return $this->getAssetForUri($uri) !== null;
    }

    public function getAssetForUri(string $uri, bool $noCache = false): ?AssetInterface
    {
        $this->assertUri($uri);
        $memoryManagerKey = self::MEMORY_MANAGER_KEY_PREFIX . $uri;
        if (!$noCache && Manager::hasObject($memoryManagerKey)) {
            $assetEntry = Manager::getObject($memoryManagerKey);

            return $assetEntry->value;
        }

        $asset = $this->loadAssetForUri($uri);
        $assetEntry = (object)['value' => $asset];
        Manager::registerObject($assetEntry, self::MEMORY_MANAGER_KEY_PREFIX . $uri, [self::MEMORY_MANAGER_TAG]);

        return $asset;
    }

    /**
     * Returns the Asset for the given URI
     *
     * @param string $uri
     * @return AssetInterface|null
     */
    public function loadAssetForUri(string $uri): ?AssetInterface
    {
        $basePath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('publicResources');
        $uri = str_replace('..', '', $uri);
        $fullPath = $basePath . $uri;
        if (file_exists($fullPath) && is_readable($fullPath)) {
            return new Asset($uri, file_get_contents($fullPath), $this->getAssetContentType($fullPath));
        }

        return null;
    }

    /**
     * @param string $fullPath
     * @return string
     */
    private function getAssetContentType(string $fullPath)
    {
        $fileInfo = new \finfo(FILEINFO_MIME);

        $mimeType = $fileInfo->file($fullPath);
        if ('text/plain' !== substr($mimeType, 0, 10)) {
            return $mimeType;
        }

        // Try to get a better result
        switch (strrchr($fullPath, '.')) {
            case '.css':
                return 'text/css';

            case '.js':
                return 'application/javascript';

            default:
                return $mimeType;
        }
    }

    /**
     * Assert that the given URI is valid
     *
     * @param string $uri
     */
    protected function assertUri($uri)
    {
        if (!$uri) {
            throw new InvalidUriException('No URI given', 1428518305);
        }
        if (strpos($uri, '..') !== false) {
            throw new InvalidUriException('URI contains illegal characters', 1428518310);
        }
    }
}
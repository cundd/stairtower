<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

use Cundd\Stairtower\Configuration\ConfigurationManager;

class Bootstrap
{
    public function init()
    {
        $XHPROF_ROOT = __DIR__ . '/../../xhprof-0.9.4/';
        if (file_exists($XHPROF_ROOT)) {
            require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php';
            require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php';
        }

        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        $parentProjectAutoloadPath = __DIR__ . '/../../../../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } else {
            require_once $parentProjectAutoloadPath;
        }
//        require_once __DIR__ . '/AbstractCase.php';
//        require_once __DIR__ . '/AbstractDataBasedCase.php';
//        require_once __DIR__ . '/AbstractDatabaseBasedCase.php';


        ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath('dataPath', __DIR__ . '/../Resources/');
        ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath(
            'writeDataPath',
            __DIR__ . '/../../var/Temp/'
        );
    }
}

(new Bootstrap())->init();

<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:25
 */

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;

$XHPROF_ROOT = __DIR__ . '/../../xhprof-0.9.4/';
if (file_exists($XHPROF_ROOT)) {
    require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php';
    require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php';
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/AbstractCase.php';
require_once __DIR__ . '/AbstractDataBasedCase.php';
require_once __DIR__ . '/AbstractDatabaseBasedCase.php';
require_once __DIR__ . '/EmptyTestClasses.php';


ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath('dataPath', __DIR__ . '/../Resources/');
ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath('writeDataPath', __DIR__ . '/../../var/Temp/');
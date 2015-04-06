<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:04
 */

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\Session\SessionControllerTrait;
use Cundd\PersistentObjectStore\View\ViewControllerTrait;

/**
 * An abstract Controller merging the different features
 *
 * @package Cundd\PersistentObjectStore\Server\Controller
 */
abstract class AbstractFullStackController extends AbstractDocumentController
{
    use ViewControllerTrait;
    use SessionControllerTrait;
}

<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\Session\SessionControllerTrait;
use Cundd\PersistentObjectStore\View\ViewControllerInterface;
use Cundd\PersistentObjectStore\View\ViewControllerTrait;

/**
 * An abstract Controller merging the different features
 */
abstract class AbstractFullStackController extends AbstractDocumentController implements ViewControllerInterface
{
    use ViewControllerTrait;
    use SessionControllerTrait;
}

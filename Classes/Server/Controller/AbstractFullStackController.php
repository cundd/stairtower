<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Controller;

use Cundd\Stairtower\Server\Session\SessionControllerTrait;
use Cundd\Stairtower\View\ViewControllerInterface;
use Cundd\Stairtower\View\ViewControllerTrait;

/**
 * An abstract Controller merging the different features
 */
abstract class AbstractFullStackController extends AbstractDocumentController implements ViewControllerInterface
{
    use ViewControllerTrait;
    use SessionControllerTrait;
}

<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\ArrayableInterface;
use Cundd\PersistentObjectStore\Result\ResultInterface;

/**
 * Interface for filter results
 */
interface FilterResultInterface extends ResultInterface, ArrayableInterface
{

}
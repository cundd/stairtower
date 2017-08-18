<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Fixtures;

use Cundd\Stairtower\Expand\ExpandResolver;

class ExpandResolverWithCoordinatorSetter extends ExpandResolver
{
    /**
     * Sets the Document Access Coordinator
     *
     * @param \Cundd\Stairtower\DataAccess\CoordinatorInterface|object $coordinator
     * @return $this
     */
    public function setCoordinator($coordinator)
    {
        $this->coordinator = $coordinator;

        return $this;
    }
}

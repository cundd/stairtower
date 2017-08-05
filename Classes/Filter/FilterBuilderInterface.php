<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter;


/**
 * Interface for classes that transform different types of input data to comparisons
 */
interface FilterBuilderInterface
{
    /**
     * Build a Filter from the given definition
     *
     * @param array $filterDefinition
     * @return FilterInterface
     */
    public function buildFilter(array $filterDefinition): FilterInterface;
}

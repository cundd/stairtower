<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 18:05
 */

namespace Cundd\PersistentObjectStore\DataAccess;

use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Interface for the Object Finder
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
interface ObjectFinderInterface
{
    /**
     * Sets the constraints to match against
     *
     * The given constraints may be a simple dictionary defining property names and values to compare with or a Doctrine expression
     *
     * @param \Doctrine\DBAL\Query\Expression\CompositeExpression|array $constraints
     * @return $this
     */
    public function setConstraints($constraints);

    /**
     * Returns the constraints to match against
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|array $constraints
     */
    public function getConstraints();

    /**
     * Sets the map of parameters
     *
     * @param array $parameters
     * @return $this
     */
    public function setParameters($parameters);

    /**
     * Returns the map of parameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * Searches the database for objects matching the previously defined constraints
     *
     * @param $database
     * @return array Returns the matching objects
     */
    public function findInDatabase($database);

    /**
     * Returns if the given Document matches the constraints
     *
     * @param DocumentInterface $document
     * @return boolean
     */
    public function compareDataInstanceWithConstraints($document);
}
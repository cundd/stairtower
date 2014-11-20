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
 * Object Finder implementation
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 * @Injectable(scope="prototype")
 */
class ObjectFinder implements ObjectFinderInterface
{
    /**
     * @var \Doctrine\DBAL\Query\Expression\CompositeExpression|array
     */
    protected $constraints;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Returns the map of parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the map of parameters
     *
     * @param array $parameters
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Searches the database for objects matching the previously defined constraints
     *
     * @param $database
     * @return array Returns the matching objects
     */
    public function findInDatabase($database)
    {
        $matchingInstances = array();
        foreach ($database as $document) {
            if ($this->compareDataInstanceWithConstraints($document)) {
                $matchingInstances[] = $document;
            }
        }
        return $matchingInstances;
    }

    /**
     * Returns if the given Document matches the constraints
     *
     * @param DocumentInterface $document
     * @return boolean
     */
    public function compareDataInstanceWithConstraints($document)
    {
        $constraints = $this->getConstraints();
        var_dump($constraints, (string)$constraints);

        return true;
    }

    /**
     * Returns the constraints to match against
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|array $constraints
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Sets the constraints to match against
     *
     * The given constraints may be a simple dictionary defining property names and values to compare with or a Doctrine expression
     *
     * @param \Doctrine\DBAL\Query\Expression\CompositeExpression|array $constraints
     * @return $this
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }


}
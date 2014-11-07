<?php

namespace carlescliment\QueryBuilder;


class FromClause
{
    private $entity;
    private $alias;

    public function __construct($entity, $alias)
    {
        $this->entity = trim( $entity, '`' );
        $this->alias = $alias;
    }

    public function __toString()
    {
        return 'FROM `' . $this->entity . '` ' . $this->alias;
    }

	/**
	 * @return string
	 */
	public function getEntityName() {
		return $this->entity;
	}

	/**
	 * @return string
	 */
	public function getEntityAlias() {
		return $this->alias;
	}
}

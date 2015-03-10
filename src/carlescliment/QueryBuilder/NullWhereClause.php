<?php

namespace carlescliment\QueryBuilder;


class NullWhereClause
{
	protected $entity;

	public function __construct($entity)
	{
		$this->entity = $entity;
	}

	public function __toString()
	{
		return $this->entity . ' IS NULL';
	}

	public function addQueryParameters($query)
	{

	}
}
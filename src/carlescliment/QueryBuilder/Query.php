<?php

namespace carlescliment\QueryBuilder;


abstract class Query
{

	protected $query;
	protected $parameters = array();

	public function __construct($query_str) {
		$this->query = $query_str;
	}

	public function setParameter($key, $value) {
		$this->query = str_replace(':'.$key, $value, $this->query);
	}

	public abstract function getResult();

	public abstract function setMaxResults( $max_results );

	public abstract function setFirstResult( $first_result );

	public abstract function getSingleScalarResult();

	/**
	 * @return string
	 */
	public function getQuery() {
		return (string)$this->query;
	}

}
<?php

namespace carlescliment\QueryBuilder;


class QueryBuilder
{

	protected $em;
	protected $selectClause;
	protected $fromClause;
	protected $joins = array();
	protected $wheres = array();
	protected $havings = array();
	protected $orders = array();
	protected $limit;
	protected $offset;
	protected $count;
	protected $model_filters = array();



	public function __construct(Database $em)
	{
		$this->em = $em;
	}

	public function select($select_clause)
	{
		$this->selectClause = new SelectClause($select_clause);
		return $this;
	}


	public function from($entity, $alias)
	{
		$this->fromClause = new FromClause($entity, $alias);
		return $this;
	}

	public function join($entity, $alias, $on, $join_type = 'JOIN')
	{
		$sTableHash = $this->getTableAliasHash( $entity, $alias );
		if ( isset( $this->joins[ $sTableHash ] ) ) {
			$this->joins[$sTableHash]->addCondition( $on );
		}
		else {
			$this->joins[$sTableHash] = new JoinClause($entity, $alias, $on, $join_type);
		}
		return $this;
	}

	protected function getTableAliasHash( $sTable, $sAlias ) {
		return md5( $sTable . '#' . $sAlias );
	}

	public function leftJoin($entity, $alias, $on)
	{
		return $this->join($entity, $alias, $on, 'LEFT JOIN');
	}

	public function whereAny( array $aParameters ) {
		$this->wheres[] = WhereClauseFactory::build( $aParameters );
		return $this;
	}

	public function where($entity, $value)
	{

		$this->wheres[] = WhereClauseFactory::build($entity, $value);
		return $this;
	}

	public function having($entity, $value)
	{
		$this->havings[] = WhereClauseFactory::build($entity, $value);
		return $this;
	}

	/**
	 * @return string
	 */
	public function peek() {
		$oQuery = $this->em->createQuery( $this->buildQueryString() );
		$this->setQueryParameters( $oQuery );
		return $oQuery->getQuery();
	}

	/** Alias for where */
	public function andWhere($entity, $value)
	{
		return $this->where($entity, $value);
	}


	public function orderBy($field, $order = 'ASC')
	{
		$this->orders[md5($field)] = new OrderByClause($field, $order);
		return $this;
	}

	public function clearOrderBy() {
		$this->orders = array();
		return $this;
	}

	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}


	public function offset($offset)
	{
		$this->offset = $offset;
		return $this;
	}


	public function count( $count_field )
	{
		$this->selectClause->count( $count_field );
		return $this;
	}


	public function executeIgnoreLimit()
	{
		return $this->execute(false);
	}


	public function execute( $applyLimit = true )
	{
		$query_str = $this->buildQueryString();
		$query = $this->em->createQuery($query_str);
		$this->setQueryParameters($query);
		if ( $applyLimit ) {
			$this->applyLimitToQuery($query);
		}
		return $this->selectClause->isCount() ? $query->getSingleScalarResult() : $query->getResult();
	}

	/**
	 * @return mixed
	 */
	public function rowCountNoLimit() {
		$sQuery = sprintf( 'SELECT COUNT(*) FROM ( %s ) AS `sub_ct`', $this->peek() );
		$oQuery = $this->em->createQuery( $sQuery );
		return $oQuery->getSingleScalarResult();
	}


	protected function applyLimitToQuery($query) {
		if ($this->limit) {
			$query->setMaxResults($this->limit);
		}
		if ($this->offset) {
			$query->setFirstResult($this->offset);
		}
	}


	protected function buildQueryString()
	{
		$query = "$this->selectClause $this->fromClause";
		$query .= $this->joinsToString();
		$query .= $this->wheresToString();
		$query .= $this->havingsToString();
		$query .= $this->orderByToString();
		return $query;
	}


	protected function joinsToString()
	{
		return empty($this->joins) ? '' : ' ' . implode(' ', $this->joins);
	}


	protected function wheresToString()
	{
		return empty($this->wheres) ? '' : ' WHERE ' . implode(' AND ', $this->wheres);
	}

	protected function havingsToString()
	{
		return empty($this->havings) ? '' : ' HAVING ' . implode(' AND ', $this->havings);
	}


	protected function orderByToString()
	{
		return empty($this->orders) ? '' : ' ORDER BY ' . implode(', ', $this->orders);
	}


	protected function setQueryParameters($query)
	{
		foreach ($this->wheres as $where) {
			$where->addQueryParameters($query);
		}
		foreach ($this->havings as $having) {
			$having->addQueryParameters($query);
		}
	}

	public function getModelFilters() 
	{
		return $this->model_filters;
	}

	public function addModelFilter( $sKey, $sValue ) 
	{
		$this->model_filters[$sKey] = array( $sKey, $sValue );
	}

	public function hasModelFilters() 
	{
		return !empty( $this->model_filters );
	}


}

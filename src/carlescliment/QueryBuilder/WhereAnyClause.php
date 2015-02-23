<?php

namespace carlescliment\QueryBuilder;


class WhereAnyClause
{
	protected $aConditions = null;

    public function __construct(array $aConditions)
    {
		$this->aConditions = array();
		foreach ( $aConditions as $sEntity => $value ) {
			$this->aConditions[] = WhereClauseFactory::build( $sEntity, $value );
		}
    }

    public function __toString()
    {
		$sQuery = '(';
		$i = 0;
		foreach ( $this->aConditions as $oWhere ) {
			$sQuery .= (string) $oWhere;

			if ( $i++ != count( $this->aConditions ) - 1 ) {
				$sQuery .= ' or ';
			}
		}
		$sQuery .= ')';
		return $sQuery;
    }

    public function addQueryParameters($query)
    {
		foreach( $this->aConditions as $oWhere ) {
			$oWhere->addQueryParameters( $query );
		}
    }
}
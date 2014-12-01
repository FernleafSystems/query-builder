<?php

namespace carlescliment\QueryBuilder;


class WhereAnyClause
{
	protected $aConditions = null;

    public function __construct(array $aConditions)
    {
        $this->aConditions = $aConditions;
    }

    public function __toString()
    {
		$sQuery = '(';
		$i = 0;
		foreach ( $this->aConditions as $sEntity => $value ) {
			$sQuery .= sprintf( '%s=%s', $sEntity, $this->buildQueryValue( $sEntity, $value ) );

			if ( $i++ != count( $this->aConditions ) - 1 ) {
				$sQuery .= ' or ';
			}
		}
		$sQuery .= ')';
		return $sQuery;
    }

    public function addQueryParameters($query)
    {
		foreach( $this->aConditions as $sEntity => $value ) {
			$query->setParameter( $sEntity, $value );
		}
    }

	protected function buildQueryValue( $sEntity, $value ) {
		return is_numeric( $value ) ? ":$sEntity" : sprintf( '":%s"', $sEntity );
	}
}
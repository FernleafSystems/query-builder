<?php

namespace carlescliment\QueryBuilder;


class JoinClause
{
    private $entity;
    private $alias;
    private $type;
    private $aConditions;

    public function __construct($entity, $alias, $on, $type)
    {
        $this->entity = $entity;
        $this->alias = $alias;
        $this->aConditions[] = $on;
        $this->type = $type;
    }

    public function __toString()
    {
        return $this->type . ' ' . $this->entity . ' ' . $this->alias . ' ON ' . implode( ' AND ', $this->aConditions );
    }

    public function addCondition( $sCondition ) {
        if ( !in_array( $sCondition, $this->aConditions ) {
            $this->aConditions[] = $sCondition;
        }
    }    
}
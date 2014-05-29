<?php

namespace carlescliment\QueryBuilder;


class NotInClause
{

    protected $entity;
    protected $values;

    public function __construct($entity, array $values)
    {
        $this->entity = $entity;
        $this->values = $values;
    }

    public function __toString()
    {
        return $this->entity . ' NOT IN (' . implode(', ', $this->getValueReferences()) . ')';
    }


    public function addQueryParameters($query)
    {
        $references = $this->getValueReferences();
        foreach($references as $i => $reference) {
            $query->setParameter($this->entity . '_' . $i, $this->values[$i]);
        }
    }

    private function getValueReferences()
    {
        $entity = $this->entity;
        return array_map(
            function($index) use ($entity) {
                return ':' . $entity . '_' . $index;
            },
            array_keys($this->values));
    }
}
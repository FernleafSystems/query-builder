<?php

namespace carlescliment\QueryBuilder;


class NotEqualClause extends ComparisonWhereClause
{

  private $strict;

  public function __construct($entity, $value, $strict = true)
  {
    parent::__construct($entity, $value);
    $this->strict = $strict;
  }

  protected function getSymbol() {
    return '<>';
  }

}
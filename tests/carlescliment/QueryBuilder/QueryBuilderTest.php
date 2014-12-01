<?php

namespace carlescliment\Test\QueryBuilder;

use carlescliment\QueryBuilder\QueryBuilder;


class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    private $builder;
    private $queryObject;
    private $om;


    public function setUp()
    {
        $this->queryObject = $this->getMockBuilder('carlescliment\QueryBuilder\Query')
            ->disableOriginalConstructor()
            ->getMock();
        $this->om = $this->getMock('carlescliment\QueryBuilder\Database');
        $this->builder = new QueryBuilder($this->om);
    }


    /**
     * @test
     */
    public function itCreatesAQuery()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->execute();
    }

    /**
     * @test
     */
    public function itAllowsFilteringByASingleExternalParameter()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE customer_uid=:customer_uid';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('customer_uid', 4)
            ->execute();
    }

	/**
	 * @test
	 */
	public function itAllowsFilteringByMultipleExternalParameter()
	{
		// Arrange
		$expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE (customer_uid=:customer_uid or customer_name=":customer_name")';

		// Expect
		$this->om->expects($this->once())
			->method('createQuery')
			->with($expected_query)
			->will($this->returnValue($this->queryObject));

		// Act
		$this->builder->select('f.*')
			->from('format', 'f')
			->join('customer', 'c', 'f.customer_id=c.id')
			->whereAny( array( 'customer_uid' => 4, 'customer_name' => 'John' ) )
			->execute();
	}


    /**
     * @test
     */
    public function itAllowsLeftJoins()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f LEFT JOIN customer c ON f.customer_id=c.id';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->leftJoin('customer', 'c', 'f.customer_id=c.id')
            ->execute();
    }

    /**
     * @test
     */
    public function itAllowsFilteringByASingleInternalParameter()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE words=:words';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('words', 4)
            ->execute();
    }

    /**
     * @test
     */
    public function itAllowsFilteringByManyParameters()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE c.uid=:c.uid AND f.format=":f.format"';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.uid', 4)
            ->andWhere('f.format', 'sample')
            ->execute();
    }


    /**
     * @test
     */
    public function itFiltersByInMatch()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE c.uid IN (:c.uid_0)';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.uid', 'in(4)')
            ->execute();
    }



    /**
     * @test
     */
    public function itFiltersByLikeMatch()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE c.name LIKE :c.name';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.name', 'like(steve%)')
            ->execute();
    }



    /**
     * @test
     */
    public function itFiltersByBetweenMatch()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f WHERE tweets BETWEEN :tweets_0 AND :tweets_1';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->where('tweets', 'between(5, 6)')
            ->execute();
    }


    /**
     * @test
     */
    public function itFiltersByMultipleInMatch()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE c.uid IN (:c.uid_0, :c.uid_1)';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.uid', 'in(4, 6)')
            ->execute();
    }


    /**
     * @test
     */
    public function itFiltersByLowerThanOperator()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE f.created < :f.created';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('f.created', 'lt("2013-12-01")')
            ->execute();
    }


    /**
     * @test
     */
    public function itFiltersByGreaterThanOperator()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE f.created > :f.created';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('f.created', 'gt("2013-12-01")')
            ->execute();
    }


    /**
     * @test
     */
    public function itFiltersByLowerOrEqualThanOperator()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE f.created <= :f.created';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('f.created', 'leqt("2013-12-01")')
            ->execute();
    }


    /**
     * @test
     */
    public function itFiltersByGreaterOrEqualThanOperator()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id WHERE f.created >= :f.created';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('f.created', 'geqt("2013-12-01")')
            ->execute();
    }


    /**
     * @test
     */
    public function itSetsTheQueryParametersInTheQueryObject()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));

        // Expect
        $this->queryObject->expects($this->at(0))
            ->method('setParameter')
            ->with('c.id', 4);

        $this->queryObject->expects($this->at(1))
            ->method('setParameter')
            ->with('f.format', 'sample');

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.id', 4)
            ->andWhere('f.format', 'sample')
            ->execute();
    }


    /**
     * @test
     */
    public function itSetsTheQueryParametersInTheQueryObjectForSimpleMatch()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));

        // Expect
        $this->queryObject->expects($this->at(0))
            ->method('setParameter')
            ->with('c.id_0', 55);

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.id', 'in(55)')
            ->execute();
    }



    /**
     * @test
     */
    public function itSetsTheQueryParametersInTheQueryObjectForMultipleMatch()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));

        // Expect
        $this->queryObject->expects($this->at(0))
            ->method('setParameter')
            ->with('c.id_0', 55);
        $this->queryObject->expects($this->at(1))
            ->method('setParameter')
            ->with('c.id_1', 56);

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('c.id', 'in(55, 56)')
            ->execute();
    }


    /**
     * @test
     */
    public function itSetsTheQueryParametersInTheQueryObjectForLowerThan()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));

        // Expect
        $this->queryObject->expects($this->at(0))
            ->method('setParameter')
            ->with('f.created', "2013-12-01");

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->where('f.created', 'lt(2013-12-01)')
            ->execute();
    }


    /**
     * @test
     */
    public function itAllowsSortingByASingleParameter()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id ORDER BY c.id DESC';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->orderBy('c.id', 'DESC')
            ->execute();
    }


    /**
     * @test
     */
    public function itAllowsSortingByMultipleParameters()
    {
        // Arrange
        $expected_query = 'SELECT f.* FROM format f JOIN customer c ON f.customer_id=c.id ORDER BY c.id DESC, c.name ASC';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('f.*')
            ->from('format', 'f')
            ->join('customer', 'c', 'f.customer_id=c.id')
            ->orderBy('c.id', 'DESC')
            ->orderBy('c.name', 'ASC')
            ->execute();
    }


    /**
     * @test
     */
    public function itAllowsLimit()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));
        $limit = 5;

        // Expect
        $this->queryObject->expects($this->once())
            ->method('setMaxResults')
            ->with($limit);

        // Act
        $this->builder->select('e')
            ->from('carlescliment\Entity\Format', 'e')
            ->limit($limit)
            ->execute();
    }


    /**
     * @test
     */
    public function itAllowsOffset()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));
        $limit = 5;
        $offset = 10;

        // Expect
        $this->queryObject->expects($this->once())
            ->method('setFirstResult')
            ->with($offset);

        // Act
        $this->builder->select('e')
            ->from('carlescliment\Entity\Format', 'e')
            ->limit($limit)
            ->offset($offset)
            ->execute();
    }


    /**
     * @test
     */
    public function itAllowsCounting()
    {
        // Arrange
        $expected_query = 'SELECT COUNT(e.id) FROM carlescliment\Entity\Format e';

        // Expect
        $this->om->expects($this->once())
            ->method('createQuery')
            ->with($expected_query)
            ->will($this->returnValue($this->queryObject));

        // Act
        $this->builder->select('e')
            ->from('carlescliment\Entity\Format', 'e')
            ->count('e.id')
            ->execute();
    }


    /**
     * @test
     */
    public function itReturnsAScalarResultWhenCounting()
    {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));

        // Expect
        $this->queryObject->expects($this->once())
            ->method('getSingleScalarResult');

        // Act
        $this->builder->select('e')
            ->from('carlescliment\Entity\Format', 'e')
            ->count('e.id')
            ->execute();
    }

    /**
     * @test
     */
    public function itExecutesQueriesIgnoringLimit() {
        // Arrange
        $this->om->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue($this->queryObject));

        // Expect
        $this->queryObject->expects($this->never())
            ->method('setMaxResults');
        $this->queryObject->expects($this->never())
            ->method('setFirstResult');

        // Act
        $this->builder->select('e')
            ->from('carlescliment\Entity\Format', 'e')
            ->limit(10)
            ->executeIgnoreLimit();
    }
}
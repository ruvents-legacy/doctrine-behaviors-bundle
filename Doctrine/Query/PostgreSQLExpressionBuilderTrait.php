<?php

namespace Ruvents\DoctrineBundle\Doctrine\Query;

use Doctrine\ORM\Query\Expr;
use Ruvents\DoctrineBundle\Doctrine\Query\Functions\OperatorFunction;

trait PostgreSQLExpressionBuilderTrait
{
    /**
     * @see Expr::eq()
     */
    abstract public function eq($x, $y);

    /**
     * @see Expr::literal()
     */
    abstract public function literal($literal);

    /**
     * @param mixed $x
     *
     * @return Expr\Comparison
     */
    public function isTrue($x)
    {
        return $this->eq($x, 'true');
    }

    /**
     * @param mixed $x
     *
     * @return Expr\Comparison
     */
    public function isFalse($x)
    {
        return $this->eq($x, 'false');
    }

    /**
     * @param mixed $x
     *
     * @return Expr\Literal
     */
    public function jsonLiteral($x)
    {
        return $this->literal(json_encode($x));
    }

    /**
     * @param string $operator
     * @param mixed  $left
     * @param mixed  $right
     *
     * @return Expr\Func
     */
    public function operator($operator, $left, $right)
    {
        if (!$operator instanceof Expr\Base) {
            $operator = $this->literal($operator);
        }

        return new Expr\Func(OperatorFunction::NAME, [$operator, $left, $right]);
    }

    /**
     * @param mixed $x
     * @param mixed $data
     *
     * @return Expr\Comparison
     */
    public function jsonContains($x, $data)
    {
        if (!$data instanceof Expr\Base) {
            $data = $this->jsonLiteral($data);
        }

        return $this->isTrue($this->operator('@>', $x, $data));
    }
}

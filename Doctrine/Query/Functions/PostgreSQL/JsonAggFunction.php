<?php

namespace Ruvents\DoctrineBundle\Doctrine\Query\Functions\PostgreSQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonAggFunction extends FunctionNode
{
    /**
     * @var Node
     */
    public $node;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->node = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'json_agg('.$this->node->dispatch($sqlWalker).')';
    }
}

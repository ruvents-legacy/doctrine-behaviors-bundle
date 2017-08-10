<?php

namespace Ruvents\DoctrineBundle\Doctrine\Query\Functions\PostgreSQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class DatePartFunction extends FunctionNode
{
    /**
     * @var Node
     */
    public $part;

    /**
     * @var Node
     */
    public $field;

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->part = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->field = $parser->StateFieldPathExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $walker)
    {
        return sprintf('date_part(%s, %s)', $this->part->dispatch($walker), $this->field->dispatch($walker));
    }
}

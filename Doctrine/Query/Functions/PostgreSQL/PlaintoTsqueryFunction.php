<?php

namespace Ruvents\DoctrineBundle\Doctrine\Query\Functions\PostgreSQL;

use Doctrine\ORM\Query\SqlWalker;

class PlaintoTsqueryFunction extends ToTsqueryFunction
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('plainto_tsquery(%s%s)',
            $this->config ? $this->config->dispatch($sqlWalker).', ' : '',
            $this->query->dispatch($sqlWalker)
        );
    }
}

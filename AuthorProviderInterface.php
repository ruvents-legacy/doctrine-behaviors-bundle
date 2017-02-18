<?php

namespace Ruvents\DoctrineBundle;

interface AuthorProviderInterface
{
    /**
     * @return mixed
     */
    public function getAuthor();
}

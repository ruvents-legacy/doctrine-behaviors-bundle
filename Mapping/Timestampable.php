<?php

namespace Ruvents\DoctrineBundle\Mapping;

/**
 * @Annotation()
 * @Target({"PROPERTY"})
 */
class Timestampable
{
    const ON_CREATE = 'create';
    const ON_UPDATE = 'update';

    /**
     * @Required
     * @Enum({"create", "update"})
     */
    public $on;
}

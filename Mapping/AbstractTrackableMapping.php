<?php

namespace Ruvents\DoctrineBundle\Mapping;

abstract class AbstractTrackableMapping
{
    const ON_PERSIST = 'persist';
    const ON_UPDATE = 'update';

    /**
     * @Required
     * @var array<string>
     */
    public $on;

    /**
     * @return bool
     */
    public function trackOnPersist()
    {
        return in_array(self::ON_PERSIST, $this->on, true);
    }

    /**
     * @return bool
     */
    public function trackOnUpdate()
    {
        return in_array(self::ON_UPDATE, $this->on, true);
    }
}

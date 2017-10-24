<?php

namespace Ruvents\DoctrineBundle\Helper;

use Doctrine\Common\Persistence\Proxy;

final class ProxyHelper
{
    /**
     * @param object $object
     */
    public static function initialize($object): void
    {
        if ($object instanceof Proxy && !$object->__isInitialized()) {
            $object->__load();
        }
    }
}

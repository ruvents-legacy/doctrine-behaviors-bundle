<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler;

interface HandlerInterface
{
    const TYPE_CLASS = 0;
    const TYPE_PROPERTY = 1;

    public static function supportsAnnotation($annotation, int $type): bool;

    public static function getSubscribedEvents(): array;
}

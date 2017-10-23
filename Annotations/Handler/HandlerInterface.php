<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler;

interface HandlerInterface
{
    public static function supportsAnnotation($annotation, int $target): bool;

    public static function getSubscribedEvents(): array;
}

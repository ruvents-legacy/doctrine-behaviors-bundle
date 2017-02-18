<?php

namespace Ruvents\DoctrineBundle\EventListener;

use Ruvents\DoctrineBundle\Doctrine\TranslatableListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TranslatableRequestListener
{
    /**
     * @var TranslatableListener
     */
    private $doctrineListener;

    /**
     * @param TranslatableListener $doctrineListener
     */
    public function __construct(TranslatableListener $doctrineListener)
    {
        $this->doctrineListener = $doctrineListener;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->doctrineListener->setCurrentLocale($event->getRequest()->getLocale());
    }
}

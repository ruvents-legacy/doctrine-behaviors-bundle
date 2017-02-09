<?php

namespace Ruvents\DoctrineBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TranslatableFieldSymfonyListener implements EventSubscriberInterface
{
    /**
     * @var TranslatableFieldDoctrineListener
     */
    private $doctrineListener;

    /**
     * @param TranslatableFieldDoctrineListener $doctrineListener
     */
    public function __construct(TranslatableFieldDoctrineListener $doctrineListener)
    {
        $this->doctrineListener = $doctrineListener;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $this->doctrineListener->setCurrentLocale($event->getRequest()->getLocale());
    }
}

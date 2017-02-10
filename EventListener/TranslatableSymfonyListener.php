<?php

namespace Ruvents\DoctrineBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TranslatableSymfonyListener implements EventSubscriberInterface
{
    /**
     * @var TranslatableDoctrineListener
     */
    private $doctrineListener;

    /**
     * @param TranslatableDoctrineListener $doctrineListener
     */
    public function __construct(TranslatableDoctrineListener $doctrineListener)
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

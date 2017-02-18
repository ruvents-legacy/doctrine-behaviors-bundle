<?php

namespace Ruvents\DoctrineBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\TranslatorInterface;

class TranslatableListener implements EventSubscriber
{
    private $translatableManager;

    /**
     * @var string
     */
    private $currentLocale;

    /**
     * @param TranslatorInterface $translatableManager
     */
    public function __construct(TranslatorInterface $translatableManager)
    {
        $this->translatableManager = $translatableManager;
    }

    /**
     * @param string $locale
     */
    final public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        if ($this->currentLocale) {
            $this->translatableManager->translate($event->getEntity(), $this->currentLocale);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        if ($this->currentLocale) {
            $this->translatableManager->translate($event->getEntity(), $this->currentLocale);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($this->currentLocale) {
            $this->translatableManager->translate($event->getEntity(), $this->currentLocale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
        ];
    }
}

<?php

namespace Ruvents\DoctrineBundle\Translations;

use Symfony\Component\HttpFoundation\RequestStack;

class TranslationsManager
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var \SplObjectStorage|TranslationsInterface[]
     */
    private $storage = [];

    public function __construct(RequestStack $requestStack, string $defaultLocale)
    {
        $this->requestStack = $requestStack;
        $this->defaultLocale = $defaultLocale;
        $this->storage = new \SplObjectStorage();
    }

    public function register(TranslationsInterface $translations)
    {
        if (!$this->storage->contains($translations)) {
            $this->storage->attach($translations);
            $translations->setCurrentLocale($this->getCurrentLocale());
        }
    }

    public function onKernelRequest()
    {
        $currentLocale = $this->getCurrentLocale();

        foreach ($this->storage as $translations) {
            $translations->setCurrentLocale($currentLocale);
        }
    }

    private function getCurrentLocale()
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $request->getLocale();
        }

        return $this->defaultLocale;
    }
}

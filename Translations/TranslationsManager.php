<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Translations;

use Symfony\Component\HttpFoundation\RequestStack;

class TranslationsManager
{
    private $requestStack;

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

    public function register(TranslationsInterface $translations): void
    {
        if (!$this->storage->contains($translations)) {
            $this->storage->attach($translations);
            $translations->setCurrentLocale($this->getCurrentLocale());
        }
    }

    public function onKernelRequest(): void
    {
        $currentLocale = $this->getCurrentLocale();

        foreach ($this->storage as $translations) {
            $translations->setCurrentLocale($currentLocale);
        }
    }

    private function getCurrentLocale(): string
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $request->getLocale();
        }

        return $this->defaultLocale;
    }
}

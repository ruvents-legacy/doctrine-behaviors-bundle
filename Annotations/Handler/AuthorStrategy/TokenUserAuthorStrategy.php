<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler\AuthorStrategy;

use Ruvents\DoctrineBundle\Annotations\Mapping\Author;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenUserAuthorStrategy implements AuthorStrategyInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldAuthor(Author $annotation, string $type, $currentValue)
    {
        $user = $this->getUser();

        if ($user instanceof UserInterface) {
            return $user->getUsername();
        }

        if (is_string($user) || (is_object($user) && method_exists($user, '__toString'))) {
            return (string)$user;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationAuthor(Author $annotation, string $targetClass, $currentValue)
    {
        $user = $this->getUser();

        if ($user instanceof $targetClass) {
            return $user;
        }

        return null;
    }

    /**
     * @return null|string|object|UserInterface
     */
    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        return $token->getUser();
    }
}

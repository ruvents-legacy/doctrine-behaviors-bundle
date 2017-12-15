<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\Metadata;

use Ruwork\DoctrineBehaviorsBundle\Mapping\Author;
use Ruwork\DoctrineBehaviorsBundle\Mapping\PersistTimestamp;
use Ruwork\DoctrineBehaviorsBundle\Mapping\Translatable;
use Ruwork\DoctrineBehaviorsBundle\Mapping\UpdateTimestamp;

final class Metadata
{
    private $class;
    private $authors = [];
    private $persistTimestamps = [];
    private $translatables = [];
    private $updateTimestamps = [];

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return Author[]
     */
    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function addAuthor(string $property, Author $author): void
    {
        $this->authors[$property] = $author;
    }

    /**
     * @return PersistTimestamp[]
     */
    public function getPersistTimestamps(): array
    {
        return $this->persistTimestamps;
    }

    public function addPersistTimestamp(string $property, PersistTimestamp $persistTimestamp): void
    {
        $this->persistTimestamps[$property] = $persistTimestamp;
    }

    /**
     * @return Translatable[]
     */
    public function getTranslatables(): array
    {
        return $this->translatables;
    }

    public function addTranslatable(string $property, Translatable $translatable): void
    {
        $this->translatables[$property] = $translatable;
    }

    /**
     * @return UpdateTimestamp[]
     */
    public function getUpdateTimestamps(): array
    {
        return $this->updateTimestamps;
    }

    public function addUpdateTimestamp(string $property, UpdateTimestamp $updateTimestamp): void
    {
        $this->updateTimestamps[$property] = $updateTimestamp;
    }
}

<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

class ClassMetadata
    extends AbstractClassMetadata
    implements TranslatableMetadataInterface, TimestampableMetadataInterface
{
    use TranslatableMetadataTrait, TimestampableMetadataTrait;
}

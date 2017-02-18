<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

class ClassMetadata
    extends AbstractClassMetadata
    implements TimestampableMetadataInterface, AuthorMetadataInterface, TranslatableMetadataInterface
{
    use TimestampableMetadataTrait, AuthorMetadataTrait, TranslatableMetadataTrait;
}

<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

class ClassMetadata
    extends AbstractClassMetadata
    implements TimestampMetadataInterface, AuthorMetadataInterface, TranslatableMetadataInterface
{
    use TimestampMetadataTrait, AuthorMetadataTrait, TranslatableMetadataTrait;
}

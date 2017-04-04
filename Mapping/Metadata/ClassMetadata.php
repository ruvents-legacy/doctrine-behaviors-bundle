<?php

namespace Ruvents\DoctrineBundle\Mapping\Metadata;

class ClassMetadata
    extends AbstractClassMetadata
    implements TimestampMetadataInterface, AuthorMetadataInterface, TranslatableMetadataInterface, UseDateMetadataInterface
{
    use TimestampMetadataTrait, AuthorMetadataTrait, TranslatableMetadataTrait, UseDateMetadataTrait;
}

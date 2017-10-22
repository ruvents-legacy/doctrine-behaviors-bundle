<?php

namespace Ruvents\DoctrineBundle;

use Ruvents\DoctrineBundle\DependencyInjection\Compiler\AddAnnotationsHandlersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuventsDoctrineBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddAnnotationsHandlersPass());
    }
}

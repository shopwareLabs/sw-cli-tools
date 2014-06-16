<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerAwarePlugin
{
    /**
     * @param ContainerBuilder $container
     */
    public function setContainer(ContainerBuilder $container);
}

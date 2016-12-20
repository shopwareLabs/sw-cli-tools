<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerAwareExtension
{
    /**
     * @param  ContainerBuilder $container
     */
    public function setContainer(ContainerBuilder $container);
}

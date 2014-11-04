<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerAwareExtension
{
    /**
     * @param  ContainerBuilder $container
     * @return void
     */
    public function setContainer(ContainerBuilder $container);
}

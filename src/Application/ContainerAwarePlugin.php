<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerAwarePlugin
{
    public function setContainer(ContainerBuilder $container);
}

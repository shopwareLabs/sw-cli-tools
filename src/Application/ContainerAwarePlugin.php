<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerAwarePlugin
{
    public function __construct(ContainerBuilder $container);

}
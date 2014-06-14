<?php

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface RepositoryAwarePlugin
{
    public function getRepositories();

}

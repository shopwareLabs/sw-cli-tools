<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerAwareExtension
{
    public function setContainer(ContainerBuilder $container);
}

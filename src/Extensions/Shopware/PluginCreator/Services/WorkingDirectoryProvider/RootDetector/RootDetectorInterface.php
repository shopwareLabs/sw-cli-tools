<?php

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector;

interface RootDetectorInterface
{
    /**
     * @param string $path
     * @return boolean
     */
    public function isRoot($path);
}

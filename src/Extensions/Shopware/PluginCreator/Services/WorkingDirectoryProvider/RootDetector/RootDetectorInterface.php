<?php

namespace Shopware\PluginCreator\Services\WorkingDirectoryProvider\RootDetector;

interface RootDetectorInterface
{
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isRoot($path);
}

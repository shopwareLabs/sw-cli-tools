<?php

namespace Plugin\ShopwareInstall\Services;
use ShopwareCli\Application\Logger;

/**
 * creates the phpstorm vcs mapping file
 *
 * Class VcsGenerator
 * @package Plugin\ShopwareInstall\Services
 */
class VcsGenerator
{
    protected $templateVcsMapping = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<project version="4">
    <component name="VcsDirectoryMappings">
        %s
    </component>
</project>
EOF;

    protected $templateVcsMappingDirectory = '<mapping directory="$PROJECT_DIR$%s" vcs="Git" />';

    public function createVcsMapping($installDir, $paths)
    {
        Logger::info("<info>Generating VCS mapping</info>");

        $dir = $installDir.'/.idea';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $mappings = array();
        foreach ($paths as $path) {
            if ($path == '/') {
                $path = '';
            }
            $mappings[] = sprintf($this->templateVcsMappingDirectory, $path);
        }

        $mappings = implode("\n", $mappings);

        file_put_contents($dir . '/vcs.xml', sprintf($this->templateVcsMapping, $mappings));

    }
}

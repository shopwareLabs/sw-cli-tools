<?php

namespace ShopwareCli\Services;

use ShopwareCli\Config;
use ShopwareCli\Services\PathProvider\PathProvider;

/**
 * Class GitIdentityEnvironment
 */
class GitIdentityEnvironment
{
    protected $wrapperFileName = 'ssh-as.sh';
    protected $keyFileName = 'ssh.key';

    protected $sshAliasTemplate = <<<'EOF'
#!/bin/bash
set -e
set -u

ssh -i $SSH_KEYFILE $@
EOF;
    /**
     * @var PathProvider
     */
    private $pathProvider;
    /**
     * @var \ShopwareCli\Config
     */
    private $config;

    public function __construct(PathProvider $pathProvider, Config $config)
    {
        $this->pathProvider = $pathProvider;
        $this->config = $config;
    }

    /**
     * Will return the path to the custom SSH key. Will return null if no
     * custom key is configured
     *
     * @throws \RuntimeException
     *
     * @return null|string
     */
    private function getCustomKey()
    {
        if (isset($this->config['sshKey'])) {
            $keyPath = $this->config['sshKey'];
            if (!file_exists($keyPath)) {
                throw new \RuntimeException("Could not find ssh key $keyPath");
            }

            return $keyPath;
        }

        $packageKey = $this->pathProvider->getCliToolPath().'/assets/ssh.key';

        if (!file_exists($packageKey)) {
            return false;
        }

        $dir = $this->pathProvider->getRuntimeDir().'/sw-cli-tools/';
        $sshKeyFile = $dir.$this->keyFileName;

        if (file_exists($sshKeyFile) || $this->writeSshKey($dir, $packageKey)) {
            return $sshKeyFile;
        }

        return null;
    }

    /**
     * @param string $dir
     * @param string $sshKeyFile
     */
    private function writeSshKey($dir, $sshKeyFile)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        $filename = $dir.$this->keyFileName;
        file_put_contents($filename, file_get_contents($sshKeyFile));
        chmod($filename, 0700);

        return file_exists($filename);
    }

    /**
     * Return path of the git wrapper file. If it doesn't exist, it will be created
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getGitWrapper()
    {
        $dir = $this->pathProvider->getRuntimeDir().'/sw-cli-tools/';

        $wrapperFile = $dir.$this->wrapperFileName;

        if (file_exists($wrapperFile) || $this->writeGitSshWrapper($dir)) {
            return $wrapperFile;
        }

        throw new \RuntimeException("Could not create git wrapper file $wrapperFile");
    }

    /**
     * Create git wrapper file
     *
     * @param  string $dir
     *
     * @return bool
     */
    private function writeGitSshWrapper($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        $filename = $dir.$this->wrapperFileName;
        file_put_contents($filename, $this->sshAliasTemplate);
        chmod($filename, 0700);

        return file_exists($filename);
    }

    /**
     * Will return an array of SSH_KEYFILE and GIT_SSH if a custom ssh key is configured
     * Else null will be returned
     *
     * @return array|null
     */
    public function getGitEnv()
    {
        if (!$this->getCustomKey()) {
            return null;
        }

        return [
            'SSH_KEYFILE' => $this->getCustomKey(),
            'GIT_SSH'     => $this->getGitWrapper()
        ];
    }
}

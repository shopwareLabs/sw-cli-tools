<?php

namespace ShopwareCli\Cache;

use ShopwareCli\Services\PathProvider\PathProvider;

class File implements CacheInterface
{
    protected $path;
    protected $info;
    /**
     * @var \ShopwareCli\Services\PathProvider\PathProvider
     */
    private $pathProvider;

    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
        $this->path = $pathProvider->getCachePath() . DIRECTORY_SEPARATOR;

        $this->info = $this->readTable();
    }

    public function write($key, $data, $valid)
    {
        $file = $this->path . $key;
        $success = file_put_contents($file, $data);

        $this->info[$key] = ['valid' => $valid];
        $this->writeTable($key, $valid);

        return $success !== false;
    }

    public function read($key)
    {
        if ($this->exists($key)) {
            $content = file_get_contents($this->path . $key);

            return $content ?: false;
        }

        return false;
    }

    public function delete($key)
    {
        unlink($this->path . $key);
        $this->writeTable();
    }

    public function exists($key)
    {
        $file = $this->path . $key;

        if (!file_exists($file)) {
            return false;
        }

        $validTime = @$this->info[$key]['valid'];

        return time() - filectime($file) < $validTime;
    }

    public function clear()
    {
        foreach ($this->getKeys() as $key) {
            unlink($this->path . $key);
            unset($this->info[$key]);
        }
        $this->writeTable();
    }

    public function getKeys()
    {
        return array_keys($this->info);
    }

    private function getInfoFile()
    {
        return $this->path . 'info.json';
    }

    private function readTable()
    {
        $file = $this->getInfoFile();
        $content = null;

        if (file_exists($file)) {
            $content = file_get_contents($file);
        }

        if (!$content) {
            $content = [];
        } else {
            $content = json_decode($content, true);
        }

        return $content;
    }

    private function writeTable()
    {
        $file = $this->getInfoFile();

        file_put_contents($file, json_encode($this->info));
    }
}

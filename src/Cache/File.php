<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Cache;

use ShopwareCli\Services\PathProvider\PathProvider;

class File implements CacheInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array|string|bool|null
     */
    protected $info;

    public function __construct(PathProvider $pathProvider)
    {
        $this->path = $pathProvider->getCachePath() . DIRECTORY_SEPARATOR;

        $this->info = $this->readTable();
    }

    public function write($key, $data, $valid): bool
    {
        $file = $this->path . $key;
        $success = file_put_contents($file, $data);

        $this->info[$key] = ['valid' => $valid];
        $this->writeTable();

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

    public function delete($key): void
    {
        unlink($this->path . $key);
        $this->writeTable();
    }

    /**
     * @param string $key
     */
    public function exists($key): bool
    {
        $file = $this->path . $key;

        if (!file_exists($file)) {
            return false;
        }

        $validTime = @$this->info[$key]['valid'];

        return time() - filectime($file) < $validTime;
    }

    public function clear(): void
    {
        foreach ($this->getKeys() as $key) {
            unlink($this->path . $key);
            unset($this->info[$key]);
        }
        $this->writeTable();
    }

    public function getKeys(): array
    {
        return array_keys($this->info);
    }

    private function getInfoFile(): string
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

    private function writeTable(): void
    {
        $file = $this->getInfoFile();

        file_put_contents($file, json_encode($this->info));
    }
}

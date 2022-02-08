<?php
namespace GWSN\FlysystemSharepoint;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\StorageAttributes;

class FlysystemSharepointAdapter implements FilesystemAdapter
{
    private string $prefix;

    private SharepointConnector $connector;

    public function __construct(
        SharepointConnector $connector,
        string $prefix = '/'
    )
    {
        $this->setConnector($connector);
        $this->setPrefix($prefix);
    }

    /**
     * @return SharepointConnector
     */
    public function getConnector(): SharepointConnector
    {
        return $this->connector;
    }

    /**
     * @param SharepointConnector $connector
     * @return FlysystemSharepointAdapter
     */
    public function setConnector(SharepointConnector $connector): FlysystemSharepointAdapter
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return FlysystemSharepointAdapter
     */
    public function setPrefix(string $prefix): FlysystemSharepointAdapter
    {
        $this->prefix = sprintf('/%s', trim($prefix, '/'));
        return $this;
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function fileExists(string $path): bool
    {
        return $this->connector->file->checkFileExists($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function directoryExists(string $path): bool
    {
        return $this->connector->folder->checkFolderExists($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return void
     * @throws \Exception
     */
    public function write(string $path, string $contents, Config $config): void
    {
        $mimeType = (isset($config['mimeType']) ? $config['mimeType'] : 'text/plain');

        $this->connector->file->writeFile($this->applyPrefix($path), $contents, $mimeType);
    }

    /**
     * @param string $path
     * @param $contents
     * @param Config $config
     * @return void
     * @throws \Exception
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        // TODO: Implement writeStream() method.
    }

    /**
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function read(string $path): string
    {
        return $this->connector->file->readFile($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @return resource
     * @throws \Exception
     */
    public function readStream(string $path)
    {
        // TODO: Implement readStream() method.
    }

    /**
     * @param string $path
     * @return void
     * @throws \Exception
     */
    public function delete(string $path): void
    {
        $this->connector->file->deleteFile($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @return void
     * @throws \Exception
     */
    public function deleteDirectory(string $path): void
    {
        $this->connector->folder->deleteFolder($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @param Config $config
     * @return void
     * @throws \Exception
     */
    public function createDirectory(string $path, Config $config): void
    {
        $this->connector->folder->createFolderRecursive($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @param string $visibility
     * @return void
     * @throws \Exception
     */
    public function setVisibility(string $path, string $visibility): void
    {
        throw new \Exception('Function not implemented');
    }

    /**
     * @param string $path
     * @return FileAttributes
     * @throws \Exception
     */
    public function visibility(string $path): FileAttributes
    {
        // TODO: Implement visibility() method.
        throw new \Exception('Function not implemented');
    }

    /**
     * @param string $path
     * @return FileAttributes
     */
    public function mimeType(string $path): FileAttributes
    {
        $this->connector->file->checkFileMimeType($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @return FileAttributes
     * @throws \Exception
     */
    public function lastModified(string $path): FileAttributes
    {
        $this->connector->file->checkFileLastModified($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @return FileAttributes
     * @throws \Exception
     */
    public function fileSize(string $path): FileAttributes
    {
        $this->connector->file->checkFileSize($this->applyPrefix($path));
    }

    /**
     * @param string $path
     * @param bool $deep
     * @return iterable|StorageAttributes[]
     * @throws \Exception
     */
    public function listContents(string $path, bool $deep): iterable
    {
        return $this->connector->folder->requestFolderItems($this->applyPrefix($path));
    }

    /**
     * @param string $source
     * @param string $destination
     * @param Config $config
     * @return void
     * @throws \Exception
     */
    public function move(string $source, string $destination, Config $config): void
    {
        $parent = explode('/', $destination);
        $fileName = array_pop($parent);

        // Create parent folders if not exists
        $parentFolder = sprintf('/%s', ltrim(implode('/', $parent), '/'));

        $this->connector->file->moveFile($this->applyPrefix($source), $this->applyPrefix($parentFolder), $fileName);
    }

    /**
     * @param string $source
     * @param string $destination
     * @param Config $config
     * @return void
     * @throws \Exception
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        $parent = explode('/', $destination);
        $fileName = array_pop($parent);

        // Create parent folders if not exists
        $parentFolder = sprintf('/%s', ltrim(implode('/', $parent), '/'));

        $this->connector->file->copyFile($this->applyPrefix($source), $this->applyPrefix($parentFolder), $fileName);
    }
    
    private function applyPrefix(string $path): string {
        return sprintf('%s/%s', $this->getPrefix(), ltrim($path));
    }
}
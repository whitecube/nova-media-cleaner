<?php

namespace Whitecube\NovaMediaCleaner\Repository;

use Illuminate\Support\Collection;
use Whitecube\NovaMediaCleaner\Exceptions\BadRepositoryArgument;
use Whitecube\NovaMediaCleaner\Exceptions\MissingRepositoryArgument;

class Storage extends Collection
{
    /**
     * The filesystem directory the Storage repository is listing
     *
     * @var string
     */
    public $directory;

    /**
     * Whether the repository is listing sub-directory results
     *
     * @var bool
     */
    public $recursive;

    /**
     * Create a new directory repository
     *
     * @param string $directory
     * @param bool $recursive
     * @return void
     */
    public function __construct($directory = null, $recursive = true)
    {
        $this->directory = $this->getRealDirectory($directory);
        $this->recursive = $recursive;

        parent::__construct(
            $this->findFiles($this->directory)
        );
    }

    /**
     * Get the given directory's real path
     *
     * @param string $directory
     * @return null|string
     * @throws \Exception
     */
    protected function getRealDirectory($directory)
    {
        if(!$directory) {
            throw new MissingRepositoryArgument(get_called_class(), 'directory');
        }

        $path = realpath($directory);

        if(!$path) {
            throw new BadRepositoryArgument(get_called_class(), 'directory', $directory, 'not an existing directory.');
        }

        if(!is_dir($path)) {
            throw new BadRepositoryArgument(get_called_class(), 'directory', $directory, 'a file, should be a directory.');
        }

        return $path;
    }

    /**
     * Find all matching files in directory
     *
     * @param string $directory
     * @param array $stack
     * @return array
     */
    protected function findFiles($directory, $path = '', $stack = [])
    {
        foreach (scandir($directory) as $item) {
            if($item === '.' || $item === '..') continue;

            $file = $directory . DIRECTORY_SEPARATOR . $item;

            if(!is_dir($file)) {
                $stack[] = $path . $item;
                continue;
            }

            if(!$this->recursive) continue;

            $stack = array_merge($stack, $this->findFiles($file, $path . $item . '/'));
        }

        return $stack;
    }
}
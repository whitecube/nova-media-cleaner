<?php

namespace Whitecube\NovaMediaCleaner;

use Whitecube\NovaMediaCleaner\Repository\Storage;
use Whitecube\NovaMediaCleaner\Results\Sources;

class Cleaner
{
    /**
     * The repository that needs cleaning
     *
     * @var \Whitecube\NovaMediaCleaner\Repository\Storage
     */
    protected $repository;

    /**
     * The available search sources
     *
     * @var \Whitecube\NovaMediaCleaner\Results\Sources
     */
    protected $sources;

    /**
     * Create a new cleaner instance
     *
     * @param \Whitecube\NovaMediaCleaner\Repository\Storage $repository
     * @param \Whitecube\NovaMediaCleaner\Results\Sources $sources
     * @return void
     */
    public function __construct(Storage $repository, Sources $sources)
    {
        $this->repository = $repository;
        $this->sources = $sources;
    }

    /**
     * Create a directory cleaner instance
     *
     * @param string $directory
     * @param bool $recursive
     * @return void
     */
    public static function directory($directory, $recursive)
    {
        $storage = (new Storage())
            ->directory($directory)
            ->recursive($recursive);

        $sources = new Sources();

        return new static($storage, $sources);
    }

    /**
     * Forward source configurations
     *
     * @param  string $file
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function analyze(Request $request)
    {
        return $this->storage
            ->scan()
            ->mapWithKeys(function($file) use ($request) {
                return [$file => $this->sources->search($file, $request)];
            });
    }

    /**
     * Forward source configurations
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->sources, $method], $arguments);
    }
}

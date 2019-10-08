<?php

namespace Whitecube\NovaMediaCleaner\Results;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;

class Sources
{
    /**
     * All the searchable repositories
     *
     * @var array
     */
    protected $repositories = [];

    /**
     * Create a new Sources index
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    // public function __construct(Request $request)
    // {
    //     $this->inAvailableResources($request);
    // }

    /**
     * Register resource as source
     *
     * @param  string $resource
     * @return \Whitecube\NovaMediaCleaner\Results\ResourceAttributes
     */
    public function inResource($resource)
    {
        return $this->registerResource($resource);
    }

    /**
     * Get all the searchable resources
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function inAvailableResources(Request $request)
    {
        foreach (Nova::availableResources($request) as $key => $resource) {
            $this->registerResource($resource, true, $request);
        }
    }

    /**
     * Get all the searchable resources
     *
     * @param  string $resource
     * @param  bool $checkSearchable
     * @param  \Illuminate\Http\Request $request
     * @return null|\Whitecube\NovaMediaCleaner\Results\ResourceAttributes
     */
    protected function registerResource($resource, $checkSearchable = false, $request = null)
    {
        $source = new ResourceAttributes($resource);

        if($checkSearchable && !$source->hasSearchableAttributes($request)) {
            return;
        }

        $this->repositories[] = $source;

        return $source;
    }

    /**
     * Launch a search query for given file
     *
     * @param  string $file
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function search($file, Request $request)
    {
        $results = collect([]);

        foreach ($this->repositories as $source) {
            $results = $results->concat($source->search($file, $request));
        }

        return $results;
    }
}

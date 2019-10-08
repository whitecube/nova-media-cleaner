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
    public function __construct(Request $request)
    {
        $this->repositories = $this->getResourceSources($request);
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

    /**
     * Get all the searchable resources
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function getResourceSources(Request $request)
    {
        $sources = [];

        foreach (Nova::availableResources($request) as $key => $resource) {
            $source = new ResourceAttributes($resource);

            if(!$source->hasSearchableAttributes($request)) {
                continue;
            }

            $sources[] = $source;
        }

        return $sources;
    }
}

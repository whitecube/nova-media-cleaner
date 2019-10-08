<?php

namespace Whitecube\NovaMediaCleaner\Results;

use Illuminate\Http\Request;

class ResourceAttributes
{
    /**
     * The resource that is being analyzed
     *
     * @var string
     */
    protected $resource;

    /**
     * Additional field types that could contain searchable attributes
     *
     * @var array
     */
    protected $extra_fields = [];

    /**
     * Additional searchable attributes
     *
     * @var array
     */
    protected $extra_attributes = [];

    /**
     * The field types that could contain searchable attributes
     *
     * @var array
     */
    static public $fields = [
        \Laravel\Nova\Fields\Avatar::class,
        \Laravel\Nova\Fields\File::class,
        \Laravel\Nova\Fields\Image::class,
        \Laravel\Nova\Fields\Trix::class,
    ];

    /**
     * Create a new ResourceAttributes source
     *
     * @param string $resource
     * @return void
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Add additional field types
     *
     * @param array $fields
     * @return this
     */
    public function addFields(array $fields)
    {
        $this->extra_fields = array_merge($this->extra_fields, $fields);

        return $this;
    }

    /**
     * Add additional attributes
     *
     * @param array $attributes
     * @return this
     */
    public function addAttributes(array $attributes)
    {
        $this->extra_attributes = array_merge($this->extra_attributes, $attributes);

        return $this;
    }

    /**
     * Launch a search query for given file
     *
     * @param  string $file
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function search($file, $request)
    {
        return $this->applySearchableAttributes($this->newQuery(), $file, $request)
            ->get()
            ->mapInto($this->resource);
    }

    /**
     * Check if the resource has attributes to look into
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function hasSearchableAttributes(Request $request)
    {
        return count($this->getSearchableAttributes($request)) > 0;
    }

    /**
     * Launch the attributes search
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getSearchableAttributes(Request $request)
    {
        $resource = $this->resource;

        $resource = new $resource($resource::newModel());

        $attributes = $this->findSearchableAttributes(
            $resource->fields($request)
        );

        return array_merge($attributes, $this->extra_attributes);
    }

    /**
     * Find all the attributes that could contain file references
     *
     * @param array $fields
     * @return array
     */
    protected function findSearchableAttributes($fields)
    {
        $attributes = [];

        foreach ($fields as $item) {
            if($this->isSearchableField($item)) {
                $attributes[] = $item->attribute;
            }

            if(!is_a($item, \Laravel\Nova\Panel::class)) continue;

            $attributes = array_merge(
                $attributes,
                $this->findSearchableAttributes($item->data)
            );
        }

        return $attributes;
    }

    /**
     * Determine if given field's attribute is searchable
     *
     * @param \Laravel\Nova\Fields\Field $field
     * @return bool
     */
    protected function isSearchableField($field)
    {
        if(!$field->attribute) {
            return false;
        }

        return in_array(
            get_class($field),
            array_merge(static::$fields, $this->extra_fields)
        );
    }

    /**
     * Determine if given field's attribute is searchable
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $file
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function applySearchableAttributes($query, $file, $request)
    {
        foreach ($this->getSearchableAttributes($request) as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%' . $file . '%');
        }

        return $query;
    }

    /**
     * Create a new Eloquent query for underlying resource
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newQuery()
    {
        return $this->resource::newModel()->newQuery();
    }
}

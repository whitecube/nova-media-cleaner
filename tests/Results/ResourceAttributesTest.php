<?php

namespace Tests\Results;

use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;
use Whitecube\NovaMediaCleaner\Results\ResourceAttributes;

class ResourceAttributesTest extends TestCase {

    /** @test */
    public function can_search_in_default_file_attributes_only()
    {
        $expected = ['avatar', 'file', 'image', 'trix'];

        $resource = \Tests\Fixtures\FakeResource::class;

        $results = new ResourceAttributes($resource);

        $attributes = $results->getSearchableAttributes(
            Request::create('/test', 'get')
        );

        $this->assertCount(count($expected), $attributes);

        foreach ($attributes as $key => $attribute) {
            $this->assertSame($expected[$key], $attribute);
        }
    }

    /** @test */
    public function can_search_in_default_file_attributes_and_appended_field_types()
    {
        $expected = ['text','avatar', 'file', 'image', 'trix'];

        $resource = \Tests\Fixtures\FakeResource::class;

        $results = (new ResourceAttributes($resource))
            ->addFields([\Laravel\Nova\Fields\Text::class]);

        $attributes = $results->getSearchableAttributes(
            Request::create('/test', 'get')
        );

        $this->assertCount(count($expected), $attributes);

        foreach ($attributes as $key => $attribute) {
            $this->assertSame($expected[$key], $attribute);
        }
    }

    /** @test */
    public function can_search_in_default_file_attributes_and_appended_attributes()
    {
        $expected = ['avatar', 'file', 'image', 'trix', 'foo', 'bar'];

        $resource = \Tests\Fixtures\FakeResource::class;

        $results = (new ResourceAttributes($resource))
            ->addAttributes(['foo', 'bar']);

        $attributes = $results->getSearchableAttributes(
            Request::create('/test', 'get')
        );

        $this->assertCount(count($expected), $attributes);

        foreach ($attributes as $key => $attribute) {
            $this->assertSame($expected[$key], $attribute);
        }
    }

    /** @test */
    public function can_check_if_resource_has_searchable_attributes()
    {
        $resource = \Tests\Fixtures\FakeResource::class;

        $results = new ResourceAttributes($resource);

        $this->assertTrue(
            $results->hasSearchableAttributes(Request::create('/test', 'get'))
        );
    }
}

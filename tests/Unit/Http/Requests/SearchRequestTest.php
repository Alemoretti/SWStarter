<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\SearchRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SearchRequestTest extends TestCase
{
    public function test_search_request_validates_query_is_required(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('query', $validator->errors()->toArray());
    }

    public function test_search_request_validates_type_is_required(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
    }

    public function test_search_request_validates_query_must_be_string(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make(['query' => 123], $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_search_request_validates_query_min_length(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make(['query' => ''], $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_search_request_validates_type_must_be_people_or_movies(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make(['type' => 'invalid'], $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_search_request_passes_with_valid_data(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make([
            'query' => 'luke',
            'type' => 'people',
        ], $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_search_request_passes_with_movies_type(): void
    {
        $request = new SearchRequest;
        $rules = $request->rules();

        $validator = Validator::make([
            'query' => 'hope',
            'type' => 'movies',
        ], $rules);

        $this->assertFalse($validator->fails());
    }
}

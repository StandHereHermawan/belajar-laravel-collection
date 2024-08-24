<?php

namespace Tests\Feature\a_membuat_collection;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = collect([1, 2, 3, 4]);

        $this->assertNotNull($collection);
        $this->assertEquals([1, 2, 3, 4], $collection->all());
    }
}

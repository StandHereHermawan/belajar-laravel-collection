<?php

namespace Tests\Feature\a_membuat_collection;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

class CollectionTest extends TestCase
{
    public function testCollection(): void
    {
        $collection = collect([1, 2, 3, 4]);

        $this->assertNotNull($collection);
        $this->assertEquals([1, 2, 3, 4], $collection->all());
    }

    public function testForEach(): void
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        assertEquals(3, $result);
        assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMapFunc()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });

        self::assertNotNull($result);
        self::assertEquals([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Terry Davis"]);
        $result = $collection->mapInto(Person::class);

        $this->assertNotNull($result);
        $this->assertEquals([new Person("Terry Davis")], $result->all());
    }

    public function testmapSpread()
    {
        $collection = collect([["Terry", "Davis"], ["Andrew", "Terry"]]);
        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . " " . $lastName;
            return new Person($fullName);
        });

        $this->assertNotNull($result);
        assertEquals([
            new Person("Terry Davis"),
            new Person("Andrew Terry")
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "Terry",
                "department" => "IT"
            ],
            [
                "name" => "Davis",
                "department" => "IT"
            ],
            [
                "name" => "Budi",
                "department" => "HR"
            ]
        ]);

        $result = $collection->mapToGroups(function ($item) {
            return [$item["department"] => $item["name"]];
        });

        self::assertNotNull($result);
        assertEquals([
            "IT" => collect(["Terry", "Davis"]),
            "HR" => collect(["Budi"])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);

        $collection3 = $collection1->zip($collection2);

        self::assertNotNull($collection3);
        assertEquals(
            [
                collect([1, 4]),
                collect([2, 5]),
                collect([3, 6]),
            ],
            $collection3->all()
        );
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);

        $collection3 = $collection1->concat($collection2);

        self::assertNotNull($collection3);
        assertEquals([1, 2, 3, 4, 5, 6], $collection3->all());
        assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = ["name", "country"];
        $collection2 = ["Terry", "USA"];

        $collection3 = collect($collection1)->combine($collection2);

        self::assertNotNull($collection3);
        assertEquals([
            "name" => "Terry",
            "country" => "USA",
        ], $collection3->all());
    }
}

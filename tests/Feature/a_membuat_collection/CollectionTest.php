<?php

namespace Tests\Feature\a_membuat_collection;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

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
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
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
        $this->assertEquals([
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
        self::assertEquals([
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
        self::assertEquals(
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
        self::assertEquals([1, 2, 3, 4, 5, 6], $collection3->all());
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = ["name", "country"];
        $collection2 = ["Terry", "USA"];

        $collection3 = collect($collection1)->combine($collection2);

        self::assertNotNull($collection3);
        self::assertEquals([
            "name" => "Terry",
            "country" => "USA",
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $result = $collection->collapse();

        self::assertNotNull($result);
        self::assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "Terry",
                "hobbies" => ["Racist", "Coding"]
            ],
            [
                "name" => "Andrew",
                "hobbies" => ["Coding", "Beat the Meat"]
            ],
            [
                "name" => "Davis",
                "hobbies" => ["Beat the Meat", "Sleeping"]
            ],
        ]);

        $hobbies = $collection->flatMap(function ($item) {
            return $item['hobbies'];
        });

        self::assertNotNull($hobbies);
        self::assertEquals(["Racist", "Coding", "Coding", "Beat the Meat", "Beat the Meat", "Sleeping"], $hobbies->all());
    }

    public function testJoinFunctionStringRepresentation()
    {
        $collection = collect(["Terry", "Andrew", "Racist", "Davis"]);

        self::assertEquals("Terry-Andrew-Racist-Davis", $collection->join("-"));
        self::assertEquals("Terry-Andrew-Racist_Davis", $collection->join("-", "_"));
        self::assertEquals("Terry, Andrew, Racist and Davis", $collection->join(", ", " and "));
    }

    public function testFilterKeyValue()
    {
        $collection = collect([
            "Terry" => 95,
            "Andrew" => 93,
            "Davis" => 92,
            "Aba" => 90,
            "Abe" => 88,
            "Abo" => 87,
        ]);

        $result = $collection->filter(function ($item, $key) {
            return $item <= 90;
        });

        self::assertNotNull($result);
        self::assertEquals([
            "Abe" => 88,
            "Aba" => 90,
            "Abo" => 87,
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $results = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });

        self::assertNotNull($results);
        self::assertNotEqualsCanonicalizing([2, 4, 6, 8, 10], $results->all());
        $this->assertEqualsCanonicalizing([1 => 2, 3 => 4, 5 => 6, 7 => 8, 9 => 10], $results->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "aba" => 91,
            "abo" => 90,
            "abe" => 89,
            "abi" => 89,
        ]);

        [$match, $didntMatch] = $collection->partition(function ($item, $key) {
            return $item <= 90;
        });

        self::assertNotNull($match);
        self::assertNotNull($didntMatch);
        self::assertEquals([
            "abo" => 90,
            "abe" => 89,
            "abi" => 89,
        ], $match->all());

        self::assertEquals([
            "aba" => 91,
        ], $didntMatch->all());
    }

    public function testTesting()
    {
        $collection = collect(["Terry", "Andrew", "System"]);

        self::assertNotNull($collection);
        self::assertTrue($collection->hasAny(0));
        self::assertTrue($collection->contains("Terry"));
        self::assertTrue($collection->contains(function ($value, $key) {
            return $value == "Terry";
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                "name" => "Arief",
                "department" => "IT"
            ],
            [
                "name" => "Hilmi",
                "department" => "IT"
            ],
            [
                "name" => "Thoriq",
                "department" => "IT"
            ],
            [
                "name" => "Bangun",
                "department" => "HR"
            ],
        ]);

        ###

        $result = $collection->groupBy("department");

        self::assertNotNull($result);
        self::assertEquals([
            "IT" => collect([
                [
                    "name" => "Arief",
                    "department" => "IT"
                ],
                [
                    "name" => "Hilmi",
                    "department" => "IT"
                ],
                [
                    "name" => "Thoriq",
                    "department" => "IT"
                ]
            ]),
            "HR" => collect([
                [
                    "name" => "Bangun",
                    "department" => "HR"
                ]
            ])
        ], $result->all());

        ###

        self::assertEquals([
            "IT" => collect([
                [
                    "name" => "Arief",
                    "department" => "IT"
                ],
                [
                    "name" => "Hilmi",
                    "department" => "IT"
                ],
                [
                    "name" => "Thoriq",
                    "department" => "IT"
                ]
            ]),
            "HR" => collect([
                [
                    "name" => "Bangun",
                    "department" => "HR"
                ]
            ])
        ], $collection->groupBy(function ($value, $key) {
            return $value['department'];
        })->all());

        ###

        $result = $collection->groupBy(function ($value, $key) {
            return $value['department'];
        });

        self::assertNotNull($result);
        self::assertEquals([
            "IT" => collect([
                [
                    "name" => "Arief",
                    "department" => "IT"
                ],
                [
                    "name" => "Hilmi",
                    "department" => "IT"
                ],
                [
                    "name" => "Thoriq",
                    "department" => "IT"
                ]
            ]),
            "HR" => collect([
                [
                    "name" => "Bangun",
                    "department" => "HR"
                ]
            ])
        ], $result->all());

        ###

        $result = $collection->groupBy(function ($value, $key) {
            return strtolower($value['department']);
        });

        self::assertNotNull($result);
        self::assertEquals([
            "it" => collect([
                [
                    "name" => "Arief",
                    "department" => "IT"
                ],
                [
                    "name" => "Hilmi",
                    "department" => "IT"
                ],
                [
                    "name" => "Thoriq",
                    "department" => "IT"
                ]
            ]),
            "hr" => collect([
                [
                    "name" => "Bangun",
                    "department" => "HR"
                ]
            ])
        ], $result->all());
    }

    public function testSlicing()
    {
        $collection = collect();
        $collection->push(1, 2, 3, 4, 5, 6, 7, 8, 9);

        $result = $collection->slice(3);

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9], $result->all());

        $result = $collection->slice(3, 2);

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([3 => 4, 4 => 5], $result->all());
    }

    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->take(3);

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all());

        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([1, 2], $result->all());

        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([1, 2], $result->all());

        ###

        $collection = collect([1, 2, 3, 1, 2, 3, 1, 2, 3]);
        $result = $collection->take(3);

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all());

        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([1, 2], $result->all());

        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([1, 2], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->skip(3);

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9], $result->all());

        $result = $collection->skipUntil(function ($value, $key) {
            return $value == 3;
        });

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9], $result->all());

        $result = $collection->skipWhile(function ($value, $key) {
            return $value < 3;
        });

        self::assertNotNull($result);
        $this->assertEqualsCanonicalizing([2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9], $result->all());
    }

    public function testChunked()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->chunk(3);

        self::assertNotNull($result);
        self::assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        self::assertEqualsCanonicalizing([3 => 4, 4 => 5, 5 => 6], $result->all()[1]->all());
        self::assertEqualsCanonicalizing([6 => 7, 7 => 8, 8 => 9], $result->all()[2]->all());

        ###

        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->chunk(3);

        self::assertNotNull($result);
        self::assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        self::assertEqualsCanonicalizing([3 => 4, 4 => 5, 5 => 6], $result->all()[1]->all());
        self::assertEqualsCanonicalizing([6 => 7, 7 => 8, 8 => 9], $result->all()[2]->all());
        self::assertEqualsCanonicalizing([9 => 10], $result->all()[3]->all());
    }

    public function testFirst()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->first();

        self::assertNotNull($result);
        self::assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });

        self::assertNotNull($result);
        assertEquals(6, $result);
    }

    public function testLast()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->last();

        self::assertNotNull($result);
        self::assertEquals(9, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 5;
        });

        self::assertNotNull($result);
        assertEquals(4, $result);
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->random();

        self::assertNotNull($result);
        self::assertTrue(in_array($result, [1, 2, 3, 4, 5, 6, 7, 8, 9]));

        $results = $collection->random(5);

        self::assertNotNull($result);

        foreach ($results as $key => $result) {
            self::assertTrue(in_array($result, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
        }
    }

    public function testCheckingExistence()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        self::assertTrue($collection->isNotEmpty());
        self::assertFalse($collection->isEmpty());
        self::assertTrue($collection->contains(8));
        self::assertFalse($collection->contains(10));

        for ($i = 0; $i < $collection->count(); $i++) {
            self::assertTrue($collection->contains(function ($value, $key) use ($i) {
                return $value == ($i + 1);
            }));
        }
    }
}

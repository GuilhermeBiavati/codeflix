<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Error;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class, 1)->create();

        $categories = Category::all();

        $this->assertCount(1, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id', 'name', 'is_active', 'description', 'created_at', 'updated_at', 'deleted_at'
        ], $categoryKey);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test1'
        ]);

        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertNull($category->is_active);
        $this->assertTrue(Uuid::isValid($category->id));

        $category = Category::create([
            'name' => 'test1',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test1',
            'description' => 'teste'
        ]);

        $this->assertEquals('teste', $category->description);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create(['description' => 'test_description', 'is_active' => false])->first();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_name_updated',
            'is_active' => true
        ];

        $category->update($data);


        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create(['description' => 'test_description', 'is_active' => false])->first();

        $category->delete();

        $this->assertSoftDeleted($category->getTable(), $category->toArray());
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', $this->category->id));
        $response->assertStatus(200)->assertJson($this->category->toArray());
    }


    public function testInvalidationData()
    {

        $data = [
            'name' => ''
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');


        $data = [
            'name' => str_repeat('a', 256),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'a'];

        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $this->assertStore($data,  $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);
        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];
        $this->assertStore($data,  $data + ['description' => 'description', 'is_active' => false]);
    }

    public function testUpdate()
    {

        $this->category = factory(Category::class)->create([
            'description' => 'teste',
            'is_active' => false
        ]);

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => true
        ];

        $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $data = [
            'name' => 'test',
            'description' => '',
        ];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
        $data['description'] = 'teste';
        $this->assertUpdate($data, array_merge($data, ['description' => 'teste']));
        $data['description'] = null;
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }

    public function testDestroy()
    {
        $this->json('DELETE', route('categories.destroy', $this->category->id));
        $this->assertSoftDeleted($this->category->getTable(), $this->category->toArray());
    }


    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', $this->category);
    }

    protected function model()
    {
        return Category::class;
    }
}

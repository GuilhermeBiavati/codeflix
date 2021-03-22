<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Tests\TestCase;

class VideoControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;
    private $category;
    private $genre;


    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create(['opened' => false]);

        // $this->category = factory(Category::class)->create();
        // $this->genre = factory(Genre::class)->create();

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_lauched' => '2010',
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            // 'categories_id' => [$this->category->id],
            // 'genres_id' => [$this->genre->id],
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response->assertStatus(200)->assertJson([$this->video->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', $this->video->id));
        $response->assertStatus(200)->assertJson($this->video->toArray());
    }


    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_lauched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => '',
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a', 256),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 'fsdaf',
        ];

        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }


    public function testInvalidationLauchedField()
    {
        $data = [
            'year_lauched' => 'fsdaf',
        ];

        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpenedField()
    {
        $data = [
            'opened' => 'fsdaf',
        ];

        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 'fsdaf',
        ];

        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a',
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100],
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }


    public function testStore()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->attach($category->id);
        $this->assertStore($this->sendData + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $this->sendData);
        $this->assertStore($this->sendData + ['opened' => true, 'categories_id' => [$category->id], 'genres_id' => [$genre->id]], $this->sendData + ['opened' => true]);
        $this->assertStore($this->sendData + ['rating' => Video::RATING_LIST[1], 'categories_id' => [$category->id], 'genres_id' => [$genre->id]], $this->sendData + ['rating' => Video::RATING_LIST[1]]);
    }

    public function testUpdate()
    {

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->attach($category->id);
        $this->assertUpdate($this->sendData + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]], $this->sendData);
        $this->assertUpdate($this->sendData + ['opened' => true, 'categories_id' => [$category->id], 'genres_id' => [$genre->id]], $this->sendData + ['opened' => true]);
        $this->assertUpdate($this->sendData + ['rating' => Video::RATING_LIST[1], 'categories_id' => [$category->id], 'genres_id' => [$genre->id]], $this->sendData + ['rating' => Video::RATING_LIST[1]]);
    }


    // public function testSave()
    // {

    //     $category = factory(Category::class)->create();
    //     $genre = factory(Genre::class)->create();

    //     $data = [
    //         [
    //             'send_data' => $this->sendData,
    //             'test_data' => $this->sendData + ['opened' => true]
    //         ],
    //         [
    //             'send_data' => $this->sendData + ['opened' => true],
    //             'test_data' => $this->sendData + ['opened' => true]
    //         ]
    //     ];
    // }

    // public function testRollbackStore()
    // {

    //     $category = factory(Category::class)->create();
    //     $genre = factory(Genre::class)->create();
    //     $genre->categories()->attach($category->id);

    //     $controller = \Mockery::mock(VideoController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    //     $controller->shouldReceive('validate')->withAnyArgs()->andReturn($this->sendData + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]]);
    //     $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);
    //     $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException());
    //     $request = \Mockery::mock(Request::class);
    //     $request->shouldReceive('get')->withAnyArgs()->andReturn([]);

    //     $hasError = false;
    //     try {
    //         $controller->store($request);
    //     } catch (TestException $exception) {
    //         $hasError = true;

    //         $this->assertCount(1, Video::all());
    //     }
    //     $this->assertTrue($hasError);
    // }

    // public function testRollbackUpdate()
    // {
    //     $request = \Mockery::mock(Request::class);
    //     $request->shouldReceive('get')->withAnyArgs()->andReturn([]);
    //     $controller = \Mockery::mock(VideoController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    //     $controller->shouldReceive('findOrFail')->withAnyArgs()->andReturn($this->video);
    //     $controller->shouldReceive('validate')->withAnyArgs()->andReturn($this->sendData + ['opened' => true]);
    //     $controller->shouldReceive('rulesUpdate')->withAnyArgs()->andReturn([]);
    //     $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException());

    //     $hasError = false;
    //     try {
    //         $controller->update($request, 'sdfa');
    //     } catch (TestException $exception) {
    //         $hasError = true;
    //         $this->assertCount(1, Video::all());
    //     }
    //     $this->assertTrue($hasError);
    // }

    public function testDestroy()
    {
        $this->json('DELETE', route('videos.destroy', $this->video->id));
        $this->assertSoftDeleted($this->video->getTable(), $this->video->toArray());
    }


    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', $this->video->id);
    }

    protected function model()
    {
        return Video::class;
    }
}

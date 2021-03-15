<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Tests\TestCase;

class VideoControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();
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


    public function testStore()
    {

        $data = [
            [
                'name' => 'test',
                'type' => Video::TYPE_ACTOR
            ],
            [
                'name' => 'test',
                'type' => Video::TYPE_DIRECTOR
            ]
        ];

        foreach ($data as $key => $value) {
            $this->assertStore($value, $value + ['deleted_at' => null]);
        }
    }

    public function testUpdate()
    {
        $this->video = factory(Video::class)->create([
            'type' => Video::TYPE_ACTOR

        ]);
        $data = [
            'name' => 'test',
            'type' => Video::TYPE_DIRECTOR
        ];
        $this->assertUpdate($data, $data + ['deleted_at' => null]);
    }

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
        return route('videos.update', $this->video);
    }

    protected function model()
    {
        return Video::class;
    }
}

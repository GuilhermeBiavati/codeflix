<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class VideoTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Video::class, 1)->create();

        $videos = Video::all();

        $this->assertCount(1, $videos);

        $menberKey = array_keys($videos->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id', 'title',
            'description',
            'year_lauched',
            'opened',
            'rating',
            'duration',
            'created_at', 'updated_at', 'deleted_at'
        ], $menberKey);
    }

    public function testCreate()
    {
        $video = factory(Video::class)->create([
            'title' => 'test1',
            'description' => 'description',
            'year_lauched' => '2020',
            'opened' => false,
            'rating' => Video::RATING_LIST[0],
            'duration' => 99,
        ])->first();

        $this->assertEquals('test1', $video->title);
        $this->assertEquals('description', $video->description);
        $this->assertEquals('2020', $video->year_lauched);
        $this->assertFalse($video->opened);
        $this->assertEquals(Video::RATING_LIST[0], $video->rating);
        $this->assertEquals(99, $video->duration);
        $this->assertTrue(Uuid::isValid($video->id));
    }

    public function testUpdate()
    {
        $video = factory(Video::class)->create()->first();

        $data = [

            'title' => 'test1',
            'description' => 'description',
            'year_lauched' => '2020',
            'opened' => false,
            'rating' => Video::RATING_LIST[0],
            'duration' => 99

        ];

        $video->update($data);


        foreach ($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete()
    {
        $video = factory(Video::class)->create()->first();

        $video->delete();

        $this->assertSoftDeleted($video->getTable(), $video->toArray());
    }
}

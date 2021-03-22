<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class VideoTest extends TestCase
{

    use DatabaseMigrations;

    private $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => 'title',
            'description' => 'description',
            'year_lauched' => '2010',
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try {
            Video::create([

                'categories_id' => [1, 2, 3]
            ]);
        } catch (QueryException $exception) {
            $hasError = true;
            $this->assertCount(0, Video::all());
        }

        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $video = factory(Video::class)->create();
        $hasError = false;
        $oldTitle = $video->title;
        try {
            $video->update([
                'title' => 'title',
                'description' => 'description',
                'year_lauched' => '2010',
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [1, 2, 3]
            ]);
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('videos', [
                'title' => $oldTitle
            ]);
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testList()
    {
        factory(Video::class, 1)->create()->first();

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

    public function testCreateWithBasicFields()
    {
        $video = Video::create($this->data)->refresh();
        $this->assertTrue(Uuid::isValid($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);
        $video = Video::create($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', ['opened' => true]);
    }

    public function testCreateWithRelarions()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]]);
        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testUpdateWithBasicFields()
    {
        $video = factory(Video::class)->create(['opened' => false]);
        $video->update($this->data);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = factory(Video::class)->create(['opened' => true]);
        $video->update($this->data);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    public function testUpdateWithRelarions()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data);
        $video->update($this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas(
            'category_video',
            [
                'video_id' => $videoId,
                'category_id' => $categoryId,
            ]
        );
    }
    public function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas(
            'genre_video',
            [
                'video_id' => $videoId,
                'genre_id' => $genreId,
            ]
        );
    }

    public function testHanleRelations()
    {
        $video = factory(Video::class)->create();
        Video::handleRelations($video, []);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        $category = factory(Category::class)->create();

        Video::handleRelations($video, ['categories_id' => [$category->id]]);
        $video->refresh();
        $this->assertCount(1, $video->categories);

        $genre = factory(Genre::class)->create();
        Video::handleRelations($video, ['genres_id' => [$genre->id]]);
        $video->refresh();

        $this->assertCount(1, $video->genres);
    }


    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();

        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[0]],
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[1], $categoriesId[2]],
        ]);

        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);


        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $video->id
        ]);


        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $video->id
        ]);
    }

    public function testSyncGenres()
    {
        $genresId = factory(Genre::class, 3)->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();

        Video::handleRelations($video, [
            'genres_id' => [$genresId[0]],
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'genres_id' => [$genresId[1], $genresId[2]],
        ]);

        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);


        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $video->id
        ]);


        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[2],
            'video_id' => $video->id
        ]);
    }

    public function testDelete()
    {
        $video = factory(Video::class)->create()->first();

        $video->delete();

        $this->assertSoftDeleted($video->getTable(), $video->toArray());
    }
}

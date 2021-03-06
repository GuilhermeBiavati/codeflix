<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Tests\Traits\TestUploads;

class VideoControllerUploadsTest extends BaseVideoControllerTestCase
{

    use TestValidations, TestUploads, TestSaves;

    public function testInvalidationVideoField()
    {
        $this->assertInvalidationFile('video_file', 'mp4', 1000000, 'mimetypes', ['values' => 'video/mp4']);
    }

    public function testStoreWithFiles()
    {

        UploadedFile::fake()->image('image.jpg');
        
        Storage::fake();
        $files = $this->getFiles();

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ] + $files
        );

        $response->assertStatus(201);

        $id = $response->json('id');
        foreach ($files as $file) {
            Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    public function testUpdateWithFiles()
    {
        Storage::fake();
        $files = $this->getFiles();

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id]
            ] + $files
        );

        $response->assertStatus(200);

        $id = $response->json('id');
        foreach ($files as $file) {
            Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create('video_file.mp4')
        ];
    }
}

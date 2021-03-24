<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Composer\DependencyResolver\Transaction;
use Event;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\Exceptions\TestException;

class VideoUploadTest extends BaseVideoTestCase
{
  public function testCreateWithFiles()
  {
    Storage::fake();
    $video = Video::create($this->data + [
      'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
      'video_file' => UploadedFile::fake()->image('video.jpg'),
    ]);

    Storage::assertExists("{$video->id}/{$video->thumb_file}");
    Storage::assertExists("{$video->id}/{$video->video_file}");
  }

  public function testCreateIfRollbackFiles()
  {
    Storage::fake();
    Event::listen(TransactionCommitted::class, function () {
      throw new TestException();
    });

    $hasError = false;

    try {
      Video::create($this->data + [
        'video_file' => UploadedFile::fake()->create('video.mp4'),
        'thumb_file' => UploadedFile::fake()->image('thumb.mp4'),
      ]);
    } catch (TestException $e) {
      $this->assertCount(0, Storage::allFiles());
      $hasError = true;
    }

    $this->assertTrue($hasError);
  }
}

<?php

namespace Tests\Prod\Models\Traits;

use Config;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tests\Stubs\Models\UploadFileStub;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorages;

class UploadFilesProdTest extends TestCase
{

    use TestStorages, TestProd;

    private $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfProd();
        $this->object = new UploadFileStub();
        Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
    }

    public function testUploadFile()
    {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->object->uploadFile($file);
        Storage::assertExists("1/{$file->hashName()}");
    }


    public function testUploadFiles()
    {

        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('video.mp4');
        $this->object->uploadFiles([$file, $file2]);
        Storage::assertExists("1/{$file->hashName()}");
        Storage::assertExists("1/{$file2->hashName()}");
    }

    public function testDeleteOldFiles()
    {

        $file1 = UploadedFile::fake()->create('video1.mp4')->size(1);
        $file2 = UploadedFile::fake()->create('video2.mp4')->size(1);
        $this->object->uploadFiles([$file1, $file2]);
        $this->object->deleteOldFiles();
        $this->assertCount(2, Storage::allFiles());

        $this->object->oldFiles = [$file1->hashName()];
        $this->object->deleteOldFiles();
        Storage::assertMissing("1/{$file1->hashName()}");
        Storage::assertExists("1/{$file2->hashName()}");
    }


    public function testDeleteFile()
    {

        $file = UploadedFile::fake()->create('video.mp4');
        $this->object->uploadFile($file);
        $fileName = $file->hashName();
        $this->object->deleteFile($fileName);
        Storage::assertMissing("1/{$fileName}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->object->uploadFile($file);
        $this->object->deleteFile($file);
        Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFiles()
    {

        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('video.mp4');

        $this->object->uploadFiles([$file, $file2]);
        $this->object->deleteFiles([$file->hashName(), $file2]);

        Storage::assertMissing("1/{$file->hashName()}");
        Storage::assertMissing("1/{$file2->hashName()}");
    }
}

<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use SoftDeletes, Uuid, UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    protected $table = 'videos';

    protected $fillable = ['title', 'description', 'opened', 'year_lauched', 'rating', 'duration', 'video_file', 'thumb_file'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_lauched' => 'integer',
        'duration' => 'integer'
    ];

    // public $increment = false;
    public $incrementing = false;
    public static $fileFields = ['video_file', 'thumb_file'];

    // Evitar realizar regra de negocio nos Controllers
    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);

        try {
            DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            // Upload de arquivos
            $obj->uploadFiles($files);
            DB::commit();
            return $obj;
        } catch (Exception $exception) {
            if (isset($obj)) {
                //Excluir os arquivos de upload
                $obj->deleteFiles($files);
            }
            DB::rollBack();
            throw $exception;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {

        $files = $this->extractFiles($attributes);

        try {
            DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);

            if ($saved) {
                $this->uploadFiles($files);
            }

            DB::commit();

            if ($saved && count($files)) {
                $this->deleteOldFiles();
            }
            return $saved;
        } catch (Exception $exception) {

            //Excluir os arquivos de upload
            $this->deleteFiles($files);

            DB::rollBack();
            throw $exception;
        }
    }

    public static function handleRelations(Video $video, array $attributes)
    {
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }
        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    public function uploadDir()
    {
        return $this->id;
    }
}

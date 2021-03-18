<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VideoStub extends Model
{
    protected $table = 'videos_stubs';
    protected $fillable = [
        'title',
        'description',
        'year_lauched',
        'opened',
        'rating',
        'duration'
    ];

    public static function createTable()
    {
        Schema::create('videos_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            $table->text('year_lauched');
            $table->boolean('opened')->default(false);
            $table->string('rating', 3);
            $table->smallInteger('duration');
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        Schema::dropIfExists('videos_stubs');
    }
}

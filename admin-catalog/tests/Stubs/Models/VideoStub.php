<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CastMenberStub extends Model
{
    protected $table = 'cast_menbers_stubs';
    protected $fillable = [
        'title',
        'description',
        'year_lauched',
        'opened',
        'rating',
        'duration'
    ];

    protected $dates = ['deleted_at'];

    public static function createTable()
    {
        Schema::create('cast_menbers_stubs', function (Blueprint $table) {
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
        Schema::dropIfExists('cast_menbers_stubs');
    }
}

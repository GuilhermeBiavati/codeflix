<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GenreStub extends Model
{
    protected $table = 'genre_stubs';
    protected $fillable = [
        'name'
    ];

    public static function createTable()
    {
        Schema::create('genre_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        Schema::dropIfExists('genre_stubs');
    }
}

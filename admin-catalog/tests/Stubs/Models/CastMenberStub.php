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
        'name', 'type'
    ];

    public static function createTable()
    {
        Schema::create('cast_menbers_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->smallInteger('type');
            $table->timestamps();
        });
    }

    public static function dropTable()
    {
        Schema::dropIfExists('cast_menbers_stubs');
    }
}

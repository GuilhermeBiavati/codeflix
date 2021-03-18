<?php

namespace Tests\Unit\Models;

use App\Models\CastMenber;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class CastMenberTest extends TestCase
{
    private $menber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->menber = new CastMenber();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillableAttribute()
    {
        $fillable = ['name', 'type'];

        $this->assertEquals($fillable, $this->menber->getFillable());
    }

    public function testIfUseTraitsAttribute()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];

        $menberTraits = array_keys(class_uses(CastMenber::class));
        $this->assertEquals($traits, $menberTraits);
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string'];

        $this->assertEquals($casts, $this->menber->getCasts());
    }

    public function testIncrementingAttribute()
    {

        $this->assertFalse($this->menber->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->menber->getDates());
        }
        $this->assertCount(count($dates), $this->menber->getDates());
    }
}

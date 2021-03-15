<?php

namespace Tests\Feature\Models;

use App\Models\CastMenber;
use Error;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CastMenberTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(CastMenber::class, 1)->create();

        $castmenbers = CastMenber::all();

        $this->assertCount(1, $castmenbers);

        $menberKey = array_keys($castmenbers->first()->getAttributes());

        $this->assertEqualsCanonicalizing([
            'id', 'name', 'type', 'created_at', 'updated_at', 'deleted_at'
        ], $menberKey);
    }

    public function testCreate()
    {
        $castmenber = CastMenber::create([
            'name' => 'test1',
            'type' => CastMenber::TYPE_DIRECTOR
        ]);

        $this->assertEquals('test1', $castmenber->name);
        $this->assertTrue(Uuid::isValid($castmenber->id));
        $this->assertEquals(CastMenber::TYPE_DIRECTOR, $castmenber->type);
    }

    public function testUpdate()
    {
        $castmenber = factory(CastMenber::class)->create(['type' => CastMenber::TYPE_DIRECTOR])->first();

        $data = [
            'name' => 'test_name_updated',
            'type' => CastMenber::TYPE_ACTOR
        ];

        $castmenber->update($data);


        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castmenber->{$key});
        }
    }

    public function testDelete()
    {
        $castmenber = factory(CastMenber::class)->create()->first();

        $castmenber->delete();

        $this->assertSoftDeleted($castmenber->getTable(), $castmenber->toArray());
    }
}

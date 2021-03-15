<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMenber;
use Illuminate\Http\Request;

class CastMenberController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',', [CastMenber::TYPE_ACTOR, CastMenber::TYPE_DIRECTOR])
        ];
    }

    protected function model()
    {
        return CastMenber::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }
    protected function rulesUpdate()
    {
        return $this->rules;
    }
}

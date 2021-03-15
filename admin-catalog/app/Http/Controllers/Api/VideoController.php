<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_lauched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => 'required|array|exists:genres,id'
        ];
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());

        $video = DB::transaction(function () use ($validated) {
            $video = $this->model()::create($validated)->first();
            $video->categories()->sync($validated['categories_id']);
            $video->genres()->sync($validated['genres_id']);
            return $video;
        });


        return $video->refresh();
    }

    public function update(Request $request, $id)
    {
        $video = $this->findOrFail($id);
        $validated = $this->validate($request, $this->rulesUpdate());
        $video->update($validated);
        $video->categories()->sync($validated['categories_id']);
        $video->genres()->sync($validated['genres_id']);
        return $video;
    }
    protected function model()
    {
        return Video::class;
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
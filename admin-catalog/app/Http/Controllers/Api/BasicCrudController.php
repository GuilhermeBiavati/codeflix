<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{

    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());
        return $this->model()::create($validated)->refresh();
    }

    public function show($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        return $obj;
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }



    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(CategoryRequest $request)
    // {
    //     return Category::create($request->all())->refresh();
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\Category  $category
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(Category $category)
    // {
    //     return $category;
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \App\Models\Category  $category
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(CategoryRequest $request, Category $category)
    // {
    //     $category->update($request->all());
    //     return $category;
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Models\Category  $category
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(Category $category)
    // {
    //     $category->delete();
    //     return response()->noContent();
    // }
}

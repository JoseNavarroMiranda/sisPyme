<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\QueryException;

class CategoriesController extends Controller
{
    public function index()
    {
        try {
                $categories = Categories::latest()->get();
                return view('categories.index', compact('categories'));
        } catch (QueryException $e) {
                return back()->with('error', 'Ocurrio un error al cargar categorias');
        }
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
                Categories::create($request->validated());
                return redirect()->route('categories.index');
        } catch (QueryException $e) {
                return back()->with('error', 'fallo en funcion de crear una categoria');
        }
    }

    public function show(Categories $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Categories $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Categories $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', 'Categoria actualizada');
    }

    public function destroy(Categories $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoria eliminada');
    }
}

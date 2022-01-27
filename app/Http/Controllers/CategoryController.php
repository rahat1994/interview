<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $isAdmin = resolve('isAdmin');
        //
        $user = Auth::user();

        if ($isAdmin) {
            $Categories = Category::all();
        } else {
            //  ONly return products of that vendor
            $user = Auth::user();
            $Categories = Category::where('author', $user->id)->get();;
        }

        return response()->json($Categories, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $user = Auth::user();
        if ($user->id) {
            try {
                $category = new Category([
                    'name' => $request->name,
                    'parent' => $request->parent,
                    'author' => $user->id,
                ]);

                $category->save();

                return response()->json($category, 200);
            } catch (Exception $e) {
                dd($e);
            }
        } else {
            $data = [
                'message' => 'NO ACCESS RIGHTS'
            ];
            return response()->json($data, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(CreateCategoryRequest $request, Category $category)
    {
        $isAdmin = resolve('isAdmin');
        if ($isAdmin) {
            $this->updateCategory($request, $category);
        }

        $user = Auth::user();

        if ($user->id == $category->author) {
            return $this->updateCategory($request, $category);
        }

        $data = [
            'message' => 'NO ACCESS RIGHTS'
        ];
        return response()->json($data, 200);
    }

    public function updateCategory(CreateCategoryRequest $request, Category $category)
    {
        //
        $user = Auth::user();
        try {

            $category->name = $request->name;
            $category->parent = $request->parent;
            $category->author =  $user->id;

            $category->save();

            return response()->json($category, 200);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
        $isAdmin = resolve('isAdmin');

        if ($isAdmin) {
            $this->deleteCategory($category);
        }

        $user = Auth::user();
        if ($user->id == $category->author) {
            return $this->deleteCategory($category);
        }

        $data = [
            'message' => 'NO ACCESS RIGHTS'
        ];
        return response()->json($data, 200);
        //

    }

    public function deleteCategory(Category $category)
    {
        $category->delete();

        $data = [
            'message' => 'Delete Successfull'
        ];
        return response()->json($data, 200);
    }
}

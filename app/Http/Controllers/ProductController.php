<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
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
        if ($isAdmin) {
            $products = Product::all();
        } else {
            //  ONly return products of that vendor

            $user = Auth::user();
            $products = Product::where('author', $user->id)->get();
        }

        return response()->json($products, 200);
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
    public function store(CreateProductRequest $request)
    {
        $isAdmin = resolve('isAdmin');
        $user = Auth::user();

        if ($user->id) {
            try {

                $product = new Product([
                    'name' => $request->name,
                    'price' => $request->price,
                    'weight' => $request->weight,
                    'categories' => json_encode($request->categories),
                    'author' => $user->id,
                ]);

                $product->save();

                return response()->json($product, 200);
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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(CreateProductRequest $request, Product $product)
    {
        //
        $isAdmin = resolve('isAdmin');
        if ($isAdmin) {
            $this->updateProduct($request, $product);
        }

        $user = Auth::user();

        if ($user->id == $product->author) {
            $this->updateProduct($request, $product);
        }

        $data = [
            'message' => 'NO ACCESS RIGHTS'
        ];
        return response()->json($data, 200);
    }

    public function updateProduct(CreateProductRequest $request, Product $product)
    {
        $user = Auth::user();
        try {
            $product->name = $request->name;

            $product->name = $request->name;
            $product->price = $request->price;
            $product->weight = $request->weight;
            $product->categories = json_encode($request->categories);
            $product->author = $user->id;;

            $product->save;

            return response()->json($product, 200);
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {

        $isAdmin = resolve('isAdmin');
        if ($isAdmin) {
            $this->deleteProduct($product);
        }

        $user = Auth::user();

        if ($user->id == $product->author) {
            return $this->deleteProduct($product);
        }

        $data = [
            'message' => 'NO ACCESS RIGHTS'
        ];
        return response()->json($data, 200);
        //

    }

    public function deleteProduct(Product $product)
    {
        $product->delete();

        $data = [
            'message' => 'Delete Successfull'
        ];
        return response()->json($data, 200);
    }
}

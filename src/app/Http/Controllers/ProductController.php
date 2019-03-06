<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Product;

class ProductController extends Controller
{

    protected function validationId(Request $request) {
        return $this->validate($request, [
            'id' => 'required|integer|unique:products',
        ]);
    }

    protected function validation(Request $request, $blnValidId = false) {
        $return = false;

        if ($blnValidId) {
            $return = $this->validationId($request);
        }

        $return = $this->validate($request, [
            'name' => 'required|max:50',
            'free_shipping' => 'boolean',
            'description' => 'nullable|max:255',
            'price' => 'required|numeric|min:0.00|max:9999.99',
            'category_id' => 'nullable|integer',
        ]);
    
        return $return;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
        return Product::all();
    }

    public function show($id) {
        return Product::find($id);
    }

    public function store(Request $request) {
        $return = false;

        if ($this->validation($request, true)) {
            $product = new Product($request->all());
            $product->save();
            $return = $product;
        }

        return $return;
    }

    public function update(Request $request, $id) {
        $return = false;

        if ($this->validation($request)) {
            $product = Product::find($id);
            $product->name = $request->input('name');
            $product->free_shipping = $request->input('free_shipping');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->category_id = $request->input('category_id');
            $product->update();
            $return = $product;
        }

        return $return;
    }

    public function destroy($id) {
        if (Product::destroy($id)) {
            return new Response('Removido com sucesso!', 200);
        }
        else {
            return new Response('Erro ao remover!', 401);
        }
    }
}

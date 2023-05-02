<?php

namespace App\Http\Controllers;

use App\Models\{Product,Category};
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Verifica se há o parametro de pesquisa
        $search = $request->query("q");

        $categories = Category::all();

        foreach($categories as $i => $category){
            $categories[$i]['products'] = Product::where('category_id',$category['id'])
            ->orderBy('id','DESC')
            ->get();
        }

        // Retorna os registros
        return response()->json(['data' => $categories], 200);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, Product $product)
    {
        // Busca a instancia pelo id
        $product = Product::findOrFail($id);

        $product['category'] = Category::findOrFail($product['category_id'])['title'];

        // Retorna a instancia
        return response()->json($product, 200);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Aplica a validação dos dados da requisição
        $validator = Validator::make($request->all(), Product::$rules);

        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
            'message' => "Ocorreram error na validação dos dados",
            'error' => 'validation.error',
            'errors' =>  $validator->errors(),
            'status' => false
        ], 422);

        // Instancia a entidade criada pelo Eloquent
        $product = new Product;

        $imageName = "";
        if($request->hasFile('imageFile') && $request->file('imageFile')->isValid()) {
            $requestImage = $request->imageFile;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName()). strtotime("now") . "." . $extension;

            $requestImage->move(public_path('products'), $imageName);
        }
 
        // Define os atributos da instância
        $product->title = $request->title;
        $product->description = $request->description;
        $product->cost = $request->cost ?? null;
        $product->promotion = $request->promotion ?? null;
        $product->image = $imageName ?? null;
        $product->category_id = $request->category_id;

        try {
            // Invoca o método de salvamento da instancia no DB pelo Eloquent
            $product->save();
            // mensagem de sucesso do cadastro
            $response = response()->json([
                'message' => 'Produto cadastrado com sucesso!',
                'status' => true
            ], 201);
        } catch (\Throwable $th) {
            // mensagem de erro
            $response = response()->json([
                'message' => "Ocorreu um erro ao salvar!",
                'error' => "sql.save",
                'errors' => $th,
                'status' => false
            ], 500);
        }

        // Retorna a mensagem
        return $response;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(int $id,Request $request, Product $product)
    {
        // Instancia o registro pelo Eloquent caso exista
        $product = Product::FindOrFail($id);

        // Aplica a validação dos dados da requisição
        $validator = Validator::make($request->all(), Product::$rules);

        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
            'message' => "Ocorreram error na validação dos dados",
            'error' => 'validation.error',
            'errors' =>  $validator->errors(),
            'status' => false
        ], 422);

        $imageName = "";
        if($request->hasFile('imageFile') && $request->file('imageFile')->isValid()) {
            $requestImage = $request->imageFile;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName()). strtotime("now") . "." . $extension;

            $requestImage->move(public_path('products'), $imageName);
        }
 
        // Define os atributos da instância
        $product->title = $request->title;
        $product->description = $request->description;
        $product->cost = $request->cost ?? null;
        $product->promotion = $request->promotion ?? null;
        $product->image = $imageName ?? $request->image ?? null;
        $product->category_id = $request->category_id;

        try {
            // Invoca o método de salvamento da instancia no DB pelo Eloquent
            $product->save();
            // mensagem de sucesso do cadastro
            $response = response()->json([
                'message' => 'Produto editado com sucesso!',
                'status' => true
            ], 200);
        } catch (\Throwable $th) {
            // mensagem de erro
            $response = response()->json([
                'message' => "Ocorreu um erro ao salvar!",
                'error' => "sql.save",
                'errors' => $th,
                'status' => false
            ], 500);
        }

        // Retorna a mensagem
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id,Product $product)
    {
        // Busca a instancia pelo id
        $product = Product::findOrFail($id);

        // Invoca o método de exclusão da instancia no DB pelo Eloquent
        $product->delete();

        // Retorna a mensagem de sucesso da exclusão
        return response()->json([
            'message' => 'Produto excluído com sucesso!',
            'status' => true
        ], 200);
    }
}

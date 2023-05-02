<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $categories = Category::all();

        // Retorna os registros
        return response()->json($categories, 200);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, Category $category)
    {
        // Busca a instancia pelo id
        $category = Category::findOrFail($id);

        // Retorna a instancia
        return response()->json($category, 200);
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
        $validator = Validator::make($request->all(), Category::$rules, Category::$messages);

        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
            'message' => "Ocorreram error na validação dos dados",
            'error' => 'validation.error',
            'errors' =>  $validator->errors(),
            'status' => false
        ], 422);

        // Instancia a entidade criada pelo Eloquent
        $category = new Category;
 
        // Define os atributos da instância
        $category->title = $request->title;

        try {
            // Invoca o método de salvamento da instancia no DB pelo Eloquent
            $category->save();
            // mensagem de sucesso do cadastro
            $response = response()->json([
                'message' => 'Categoria cadastrada com sucesso!',
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(int $id,Request $request, Category $category)
    {
        // Busca a instancia pelo id
        $category = Category::findOrFail($id);
 
        // Aplica a validação dos dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:250',
                Rule::unique('categories')->ignore($id)
            ]
        ], Category::$messages);

        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
            'message' => "Ocorreram error na validação dos dados",
            'error' => 'validation.error',
            'errors' =>  $validator->errors(),
            'status' => false
        ], 422);

        // Define os atributos da instância
        $category->title = $request->title;

        try {
            // Invoca o método de salvamento da instancia no DB pelo Eloquent
            $category->save();
            // mensagem de sucesso do cadastro
            $response = response()->json([
                'message' => 'Categoria editada com sucesso!',
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id,Category $category)
    {
        // Busca a instancia pelo id
        $category = Category::findOrFail($id);

        // Invoca o método de exclusão da instancia no DB pelo Eloquent
        $category->delete();

        // Retorna a mensagem de sucesso da exclusão
        return response()->json([
            'message' => 'Categoria excluída com sucesso!',
            'status' => true
        ], 200);
    }
}

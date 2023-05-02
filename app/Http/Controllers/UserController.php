<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public $user;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        
        $users = User::all();

        // Retorna os registros
        return response()->json(['data' => $users], 200);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, User $user)
    {
        // Busca a instancia pelo id
        $user = User::findOrFail($id);

        // Retorna a instancia
        return response()->json($user, 200);
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
        $validator = Validator::make($request->all(), User::$rules, User::$messages);

        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
            'message' => "Ocorreram erros na validação dos dados",
            'error' => "validation.error",
            'errors' =>  $validator->errors(),
            'status' => false
        ], 422);

        // Instancia a entidade criada pelo Eloquent
        $user = new User;

        // Define os atributos da instância
        $user->username = $request->username;
        $user->email = $request->email;
        $user->block = $request->block ?? 0;
        $user->password = Hash::make($request->password);

        try {
            // Invoca o método de salvamento da instancia no DB pelo Eloquent
            $user->save();
            // Insere o id do usuário ao atributo da instancia
            $this->user = $user;
            // mensagem de sucesso do cadastro
            $response = response()->json([
                'message' => 'Usuário cadastrado com sucesso!', 
                'status' => true
            ], 201);
        } catch (\Throwable $th) {
            $response = response()->json([
                'message' => "Ocorreu um erro ao salvar!",
                'error' => 'sql.save',
                'errors' => $th,
                'status' => false
            ], 500);
        }
      
        return $response;
    }  

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(int $id,Request $request, User $user)
    {
        // Busca a instancia pelo id
        $user = User::findOrFail($id);

        // Aplica a validação
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users')->ignore($id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($id)
            ],
            "password" => "max:256",
            "block" => "numeric"
        ], User::$messages);
        
        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
            'message' => "Ocorreram erros na validação dos dados",
            'error' => "validation.error",
            'errors' =>  $validator->errors(),
            'status' => false
        ], 422);
    
        // Define os atributos da instância
        $user->username = $request->username;
        $user->email = $request->email;
        $user->block = $request->block ?? 0;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;

        try {
            // Invoca o método de salvamento da instancia no DB pelo Eloquent
            $user->save();
            // mensagem de sucesso da atualização
            $response = response()->json([
                'message' => 'Usuário editado com sucesso',
                'status' => true
            ], 200);
        } catch (\Throwable $th) {
            // mensagem de erro
            $response = response()->json([
                'message' => "Ocorreu um erro ao salvar!",
                'error' => 'sql.save',
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id,User $user)
    {
        // Busca a instancia pelo id
        $user = User::findOrFail($id);

        // Invoca o método de exclusão da instancia no DB pelo Eloquent
        $user->delete();

        // Retorna a mensagem de sucesso da exclusão
        return response()->json([
            'message' => 'Usuário excluído com sucesso!',
            'status' => true
        ], 200);
    }
     
}
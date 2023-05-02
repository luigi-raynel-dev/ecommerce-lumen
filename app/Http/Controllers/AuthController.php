<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{

    /**
     * Authenticate user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signIn(Request $request)
    {
        // Aplica a validação dos dados da requisição
        $validator = Validator::make($request->all(),[
          "username" => "required|string",
          "password" => "required|string"
        ]);

        // Retorna um log dos erros caso haja falha na validação
        if($validator->fails()) return response()->json([
          'message' => "Ocorreram erros na validação dos dados",
          'error' => "validation.error",
          'errors' =>  $validator->errors(),
          'status' => false
        ], 422);

        $credentials = request(['username', 'password']);
        $token = auth()->attempt($credentials);

        if (!$token) return response()->json([
          'error' => 'unauthorized',
          'status' => false
        ], 401);

        $user = User::where('username',$request->username)->first();

        // Verifica se o usuário está bloqueado
        if($user['block'] == 1){
          return response()->json([
            'status' => false,
            'error' => 'user.block',
            'message' => "Usuário bloqueado pelo administrador"
          ],403);
        }
    
        // Retorna o login válido com os dados do usuário e o token
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ],200);
    }
     
}
<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Rota para autenticação do usuário
$router->post('/auth', 'AuthController@signIn');

// Grupo de rotas para os usuários
$router->group(['prefix' => 'users','middleware' => 'auth'], function () use ($router) {
    // Rota para exibir todos os usuários
    $router->get('/', 'UserController@index');
    // Rota para exibir um usuário
    $router->get('/{id}', 'UserController@show');
    // Rota para cadastrar um usuário
    $router->post('/','UserController@store');
    // Rota para editar um usuário
    $router->put('/{id}', 'UserController@update');
    // Rota para excluir um usuário
    $router->delete('/{id}', 'UserController@destroy');
});

// Grupo de rotas para os produtos 
$router->group(['prefix' => 'uploads'], function () use ($router) {
    // Rota para exibir um arquivo
    $router->get('{folder}/{filename}', 'UploadController@show');
});

// Grupo de rotas para os produtos 
$router->group(['prefix' => 'products'], function () use ($router) {
    // Rota para exibir todos os produtos
    $router->get('/', 'ProductController@index');
    // Rota para exibir um produto
    $router->get('/{id}', 'ProductController@show');
    
    $router->group(['prefix' => '/','middleware' => 'auth'], function () use ($router) {
        // Rota para cadastrar um produto
        $router->post('/', 'ProductController@store');
        // Rota para editar um produto
        $router->post('/{id}', 'ProductController@update');
        // Rota para excluir um produto
        $router->delete('/{id}', 'ProductController@destroy');
    });
});

// Grupo de rotas para as categorias 
$router->group(['prefix' => 'categories'], function () use ($router) {
    // Rota para exibir todas as categorias
    $router->get('/', 'CategoryController@index');
    // Rota para exibir uma categoria
    $router->get('/{id}', 'CategoryController@show');

    $router->group(['prefix' => '/','middleware' => 'auth'], function () use ($router) {
        // Rota para cadastrar uma categoria
        $router->post('/', 'CategoryController@store');
        // Rota para editar uma categoria
        $router->put('/{id}', 'CategoryController@update');
        // Rota para excluir uma categoria
        $router->delete('/{id}', 'CategoryController@destroy'); 
    });
});


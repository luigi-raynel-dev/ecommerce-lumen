<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) return response()->json([
            'message' => 'A sessão expirou ou está inválida.',
            'error' => 'unauthorized'
        ],401);
        
        $user_id = $this->auth->user()->getAttribute('id');
        $request->merge(['user_id' => $user_id]);
        
        $response = $next($request);
        
        if(\method_exists($response,'content')){
            $body = json_decode($response->content(), true);
            $body['access_token'] = auth()->setTTL($_ENV['JWT_EXP'] ?? 1440)->tokenById($user_id);
            
            if (json_last_error() == JSON_ERROR_NONE) $response->setContent(json_encode($body));
        }

        return $response;
    }
}
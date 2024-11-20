<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Verifica a existência de um token no cabeçalho da requisição
        $token = $request->header('X-API-KEY');

        if (!$token || $token !== config('app.api_key')) {
            // Retorna erro 401 se o token for inválido
            return response()->json(['message' => 'Invalid API Token'], 401);
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->header('Access-Control-Allow-Origin', '*'); //aca se cambia luego por el host de angular
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization');

        return $response;
    }
}

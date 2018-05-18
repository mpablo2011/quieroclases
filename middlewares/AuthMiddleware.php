<?php

namespace App\Middleware;


class AuthMiddleware extends Middleware

{
    public function __invoke($request, $response, $next)
    {

        // Chequear si el usuario esta logueado


        $response = $next($request, $response);
        return($response);
    }


}
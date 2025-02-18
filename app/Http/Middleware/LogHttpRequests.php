<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

//Middleware para log de request e response HTTP
class LogHttpRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Captura os dados da requisição
        $requestData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ];

        Log::channel('http_logs')->info('HTTP REQUEST:', $requestData);

        // Processa a requisição e obtém a resposta
        $response = $next($request);

        // Captura os dados da resposta
        $responseData = [
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent(),
        ];

        Log::channel('http_logs')->info('HTTP RESPONSE:', $responseData);

        return $response;
    }
}

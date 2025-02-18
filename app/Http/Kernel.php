<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    protected $routeMiddleware = [
        'profile' => \App\Http\Middleware\CheckProfile::class,
        'prioritProfile' => \App\Http\Middleware\CheckProfile::class,

    ];
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class, //Permite definir quais hosts são confiáveis para acessar a aplicação.
        \App\Http\Middleware\TrustProxies::class, //Gerencia reverse proxies (como Cloudflare, AWS ELB, Nginx).
        \Illuminate\Http\Middleware\HandleCors::class, //Lida com CORS (Cross-Origin Resource Sharing), que controla quais origens externas podem acessar sua API.
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class, //Impede que usuários acessem a aplicação quando o Laravel está no modo de manutenção (php artisan down).
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Valida o tamanho máximo de dados que podem ser enviados por POST.
        \App\Http\Middleware\TrimStrings::class, // Remove espaços extras no início e fim de strings em requisições.
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Converte strings vazias ("") em valores NULL no banco de dados automaticamente.
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            'throttle:api',
            'throttle:60,1', 
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LogHttpRequests::class,//habilitar quando for debugar log da da comunicao back com front
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}

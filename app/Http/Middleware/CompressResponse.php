<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Aplicar compressão apenas em produção e para respostas apropriadas
        if (app()->environment('production')) {
            $content = $response->getContent();
            
            // Verificar se o cliente aceita compressão gzip
            $acceptEncoding = $request->header('Accept-Encoding', '');
            $supportsGzip = str_contains($acceptEncoding, 'gzip');
            
            // Aplicar apenas para JSON, HTML, CSS, JS
            $shouldCompress = $request->wantsJson() 
                || $request->is('*.css') 
                || $request->is('*.js')
                || $request->expectsJson()
                || ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html'));
            
            if ($supportsGzip && $shouldCompress && $content && strlen($content) > 1024) {
                $compressed = gzencode($content, 6);
                
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Vary', 'Accept-Encoding');
                    $response->headers->remove('Content-Length');
                }
            }
        }
        
        return $response;
    }
}

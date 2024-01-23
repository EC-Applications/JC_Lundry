<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class ModuleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check header request and determine localizaton
        if(!$request->hasHeader('moduleId'))
        {
            $errors = [];
            $errors[] = ['code' => 'moduleId', 'message' => trans('messages.module_id_required')];
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        Config::set('module.current_module_id', $request->header('moduleId'));
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\AdminRoute;
use Closure;
use Illuminate\Http\Request;

class AdminLog
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!in_array($method = $request->getMethod(), ['GET', 'OPTIONS'])) {
            $responseData = $response->getData(true);

            $adminRoute = AdminRoute::findBy($request->route());

            $data = [
                'user_id' => $request->user()['id'],
                'route_id' => $adminRoute['id'] ?? null,
                'method' => $method,
                'path' => $request->path(),
                'ip' => $request->ip(),
                'input' => $request->input(),
                'status' => $response->getStatusCode(),
                'code' => $responseData['code'] ?? '',
                'message' => $responseData['message'] ?? '',
            ];

            \App\Models\AdminLog::creates($data);
        }

        return $response;
    }
}

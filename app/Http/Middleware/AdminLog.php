<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!in_array($method = $request->getMethod(), ['GET', 'OPTIONS'])){
            $responseData = $response->getData(true);

            $data = [
                'user_id' => $request->user()['id'],
                'method' => $method,
                'path' => $request->path(),
                'ip' => $request->ip(),
                'input' => $request->input(),
                'status' => $response->getStatusCode(),
                'code' => $responseData['code']??'',
                'message' => $responseData['message']??'',
            ];

            \App\Models\AdminLog::creates($data);
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Api_User;

class AuthenticationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $errors = [];
        $header = \Auth::guard('api')->getTokenForRequest();
        if (count($header)) {
            $api_user = Api_User::where('bearer', '=', $header)->get();
            if (!count($api_user)) {
                $errors['error'] = 'Unauthenticated';
                echo json_encode($errors);die;
            }
        } else {
            $errors['error'] = 'Unauthenticated';
            echo json_encode($errors);
            exit;
        }
        return $next($request);
    }
}

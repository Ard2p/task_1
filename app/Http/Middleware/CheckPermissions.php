<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermissions
{
    protected $roles = ['dev', 'admin', 'moder', 'streamer', 'user'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $indexUser = array_keys($this->roles, $request->user()->role);
        $indexPerm = array_keys($this->roles, $role);

        // dd($this->roles, $indexUser, $indexPerm, $indexUser > $indexPerm);

        if($indexUser > $indexPerm)
            if ($request->isMethod('get')) {
                return back();
            }else{
                $data = array(
                    'status' => 'error',
                    'message' => 'Недостаточно прав!'
                );
                return response()->json($data, 403);
            }
        return $next($request);
    }
}

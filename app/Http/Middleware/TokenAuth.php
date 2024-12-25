<?php

namespace App\Http\Middleware;


use App\Models\Base\UserToken;
use App\Services\Business\ResponseService;
use App\Services\Business\UserService;
use Closure;

class TokenAuth
{

    public $userService;

    public $resp;

    public function __construct(UserService $userService, ResponseService $resp)
    {
        $this->userService = $userService;
        $this->resp        = $resp;
    }

    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $uid   = $request->input('uid', session('uid'));
        $token = $request->input('token', $request->input('tk', session('token')));
        $request->merge(['uid' => $uid, 'token' => $token]);

        // 非严格模式下，排除掉测试账号。
        //if (($strict === 'strict' && ! config('app.debug'))) {

        if (config('app.check_token')) {
            // 验证uid与token
            $token_pass = UserToken::checkToken($uid, $token);
            if ($token_pass) {
                // token验证成功, session 保存uid和token
                //                session(['uid' => $uid]);
                //                session(['token' => $token]);
                //                $key = 'handShark_'.$uid;
                //                Cache::remember($key, Carbon::today()->endOfDay(), function () use ($uid) {
                //                    dispatch(new UserActiveJob($uid, []));
                //                    return 1;
                //                });
            } else {
                if ($request->ajax() || $request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->acceptsJson()
                    || $request->input('is_mini_program')
                    || $request->input('is_tt_mini_program')
                ) {
                    // 接口返回401状态码。
                    return response($this->resp->errorResponse('登录已过期.', 401), 200);
                } else {
                    logger('TokenAuth.html.redirect', [$uid, $token]);

                    return redirect()->route('Unauthorized', ['msg' => '登录已过期.']);
                    // 直接Web浏览，返回210状态码。
                    //                    return redirect()->away('mdaction:error_211');
                }
            }
        }

        return $next($request);
    }

}
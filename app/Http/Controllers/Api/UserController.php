<?php

namespace App\Http\Controllers\Api;

use App\Services\Business\ResponseService;
use App\Services\Business\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    private $resp;
    private $userService;

    public function __construct(ResponseService $resp, UserService $userService)
    {
        $this->resp        = $resp;
        $this->userService = $userService;
        $this->middleware('token', [
            'except' => [
                'openidLogin',
            ],
        ]);
    }

    /**
     * 获取用户信息
     * /api/2022060903
     * @param  Request  $request
     * @return array|mixed
     */
    public function getPersonInfo(Request $request)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('参数错误');
        }
        try {
            $app    = app(UserService::class);
            $result = $app->getPersonInfo($params);
            if ($result === false) {
                return $this->resp->errorResponse($app->getErrorMessage(), $app->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logger('getPersonInfo.error', [$e->getMessage(), $e->getLine(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

    /**
     * 登录
     * /api/2024012901
     * @param  Request  $request
     * @return array|mixed
     */
    public function openidLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'android_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数错误！');
        }
        try {
            $params   = $request->all();
            $userInfo = $this->userService->openidLogin($params);
            if ($userInfo === false) {
                logerror('openidLogin.logerror', ['$params' => $params, $this->userService->getErrorMessage(), $this->userService->getErrorCode()]);

                return $this->resp->errorResponse($this->userService->getErrorMessage());
            }

            return $this->resp->returnData($userInfo);
        } catch (\Exception $e) {
            logerror('openidLogin.logerror', ['$params' => $request->all(), $e->getMessage(), $e->getFile(), $e->getLine()]);

            return $this->resp->errorResponse('登录失败！');
        }
    }

}
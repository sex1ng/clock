<?php

namespace App\Http\Controllers\Api;

use App\Services\Business\ResponseService;
use App\Services\Business\TargetService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TargetController extends Controller
{

    private $resp;

    public function __construct(ResponseService $resp)
    {
        $this->resp = $resp;
    }


    /**
     * 添加编辑目标
     * 2024022301
     * @param  Request  $request
     * @param  TargetService  $targetService
     * @return array|mixed
     */
    public function editTarget(Request $request, TargetService $targetService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'target_id'   => '',
            'uid'         => 'required|integer',
            'target_name' => 'required|string',
            'target_type' => 'required|integer',
            'remind_week' => 'required', // 1,2,3,4,5,6,7
            'remind_hour' => 'required', // 10:00
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $targetService->editTarget($params);
            if ($result === false) {
                return $this->resp->errorResponse($targetService->getErrorMessage(), $targetService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('editTarget.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

    /**
     * 打卡打卡
     * 2024022302
     * @param  Request  $request
     * @param  TargetService  $targetService
     * @return array|mixed
     */
    public function clockIn(Request $request, TargetService $targetService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid'         => 'required|integer',
            'target_id'   => 'required|integer',
            'clockin_img' => '',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $targetService->clockIn($params);
            if ($result === false) {
                return $this->resp->errorResponse($targetService->getErrorMessage(), $targetService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('editTarget.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

    /**
     * 获取目标列表
     * 2024022303
     * @param  Request  $request
     * @param  TargetService  $targetService
     * @return array|mixed
     */
    public function getTargetList(Request $request, TargetService $targetService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid'  => 'required|integer',
            'page' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $targetService->getTargetList($params);
            if ($result === false) {
                return $this->resp->errorResponse($targetService->getErrorMessage(), $targetService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('getTargetList.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

    /**
     * 删除目标
     * 2024022304
     * @param  Request  $request
     * @param  TargetService  $targetService
     * @return array|mixed
     */
    public function delTarget(Request $request, TargetService $targetService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid'       => 'required|integer',
            'target_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $targetService->delTarget($params);
            if ($result === false) {
                return $this->resp->errorResponse($targetService->getErrorMessage(), $targetService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('delTarget.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

}
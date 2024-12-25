<?php

namespace App\Http\Controllers\Api;

use App\Services\Business\DiaryService;
use App\Services\Business\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DiaryController extends Controller
{

    private $resp;

    public function __construct(ResponseService $resp)
    {
        $this->resp = $resp;
    }


    /**
     * 添加编辑日记
     * 2024022701
     * @param  Request  $request
     * @param  DiaryService  $diaryService
     * @return array|mixed
     */
    public function editDiary(Request $request, DiaryService $diaryService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid'      => 'required|integer',
            'title'    => 'required',
            'content'  => 'required',
            'diary_id' => '',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $diaryService->editDiary($params);
            if ($result === false) {
                return $this->resp->errorResponse($diaryService->getErrorMessage(), $diaryService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('editDiary.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

    /**
     * 获取日记列表
     * 2024022702
     * @param  Request  $request
     * @param  DiaryService  $diaryService
     * @return array|mixed
     */
    public function getDiaryList(Request $request, DiaryService $diaryService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid'  => 'required|integer',
            'page' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $diaryService->getDiaryList($params);
            if ($result === false) {
                return $this->resp->errorResponse($diaryService->getErrorMessage(), $diaryService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('editDiary.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }

    /**
     * 删除日记
     * 2024022703
     * @param  Request  $request
     * @param  DiaryService  $diaryService
     * @return array|mixed
     */
    public function delDiary(Request $request, DiaryService $diaryService)
    {
        $params    = $request->all();
        $validator = Validator::make($params, [
            'uid'      => 'required|integer',
            'diary_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->resp->errorResponse('请求参数异常');
        }

        try {
            $result = $diaryService->delDiary($params);
            if ($result === false) {
                return $this->resp->errorResponse($diaryService->getErrorMessage(), $diaryService->getErrorCode());
            }

            return $this->resp->returnData($result);
        } catch (\Exception $e) {
            logerror('delDiary.logerror', [$e->getMessage(), $e->getLine(), $e->getFile(), $params]);

            return $this->resp->errorResponse('操作失败.');
        }
    }


}
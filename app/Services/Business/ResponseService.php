<?php
namespace App\Services\Business;

class ResponseService
{
    /**
     * 返回格式
     * @param null $data
     * @param int $code
     * @param string $message
     * @return array
     */
    public function response($data = null, $code = 200, $message = '')
    {
        return ['data' => $data, 'code' => $code, 'msg' => $message];
    }

    /**
     * 返回成功数据
     * @param $data
     * @return mixed
     */
    public function returnData($data, $message = '操作成功')
    {
        return $this->response($data, 200, $message);
    }

    /**
     * 返回成功信息
     * @param string $message
     * @return mixed
     */
    public function successResponse($message = '操作成功')
    {
        return $this->response(null, 200, $message);
    }

    /**
     * 返回成失败信息
     * @param $message
     * @param int $code
     * @return mixed
     */
    public function errorResponse($message, $code = 422)
    {
        return $this->response(null, $code, $message);
    }

}
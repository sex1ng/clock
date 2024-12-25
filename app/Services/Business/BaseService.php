<?php

namespace App\Services\Business;

class BaseService
{

    const ANDROID_ID = 20296;

    const IOS_ID     = 20296;

    const WEB_ID     = 20296;

    public $successMessage = '操作成功';
    public $errorMessage = '操作失败';
    public $errorCode = 422;

    public $app_id;
    public $version;

    public function __construct()
    {
        $this->app_id  = request()->header('ua-app-id', request()->input('app_id', ''));
        $this->version = request()->header('ua-app-version', request()->input('key', ''));
        if (empty($this->app_id)) {
            $this->app_id = strstr(request()->header('user-agent'), 'ios') !== false ? self::IOS_ID : self::ANDROID_ID;
        }
    }

    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }


}
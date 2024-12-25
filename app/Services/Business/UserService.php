<?php

namespace App\Services\Business;

use App\Models\Base\UserDetail;
use App\Models\Base\UserInfo;
use App\Models\Base\UserLoginLog;
use App\Models\Base\UserToken;
use App\Models\Diary\Diary;
use App\Models\Target\Target;
use App\Models\Target\TargetClockinLog;

class UserService extends BaseService
{

    protected $userInfoModel;
    protected $userDetailModel;
    protected $userTokenModel;

    public function __construct(UserInfo $userInfoModel, UserToken $userTokenModel, UserDetail $userDetailModel)
    {
        parent::__construct();
        $this->userInfoModel   = $userInfoModel;
        $this->userTokenModel  = $userTokenModel;
        $this->userDetailModel = $userDetailModel;
    }


    /**
     * 获取用户信息
     * @param $params
     * @return false|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function getPersonInfo($params)
    {
        $uid       = $params['uid'];
        $user_info = UserInfo::query()->where('uid', $uid)->first();
        if ( ! $user_info) {
            $this->errorMessage = '未找到该用户.';

            return false;
        }

        $user_info->clockin_days = TargetClockinLog::query()->where('uid', $uid)->where('is_delete', 0)->groupBy(['date'])->get()->count();
        $user_info->target_num   = Target::query()->where('uid', $uid)->where('is_delete', 0)->count();
        $user_info->diary_num    = Diary::query()->where('uid', $uid)->where('is_delete', 0)->count();

        $user_info->token = @$params['token'] ?: '';

        return $user_info;
    }

    /**
     * 登录
     * @param $params
     * @return UserInfo|false
     */
    public function openidLogin($params)
    {
        logger('getPersonInfo', [$params]);
        $openid    = $params['openid'] ?? '';
        $androidId = $params['android_id'] ?? '';
        $app_id    = $params['app_id'] ?? BaseService::WEB_ID;
        if ($openid) {
            $userInfo = UserInfo::getUserInfoByOpenid($openid);
        } else {
            $userInfo = UserInfo::getUserInfoByAndroidId($androidId, $app_id);
        }
        $uid   = $userInfo->uid;
        $token = UserToken::updateToken($uid, $app_id);

        $params['device_id'] = 0;
        $params['uid']       = $uid;
        $params['app_id']    = $app_id;
        $params['version']   = $this->version;
        $params['ip']        = request()->getClientIp();
        $params['channel']   = request()->header('ua-channel', '');
        $params['os']        = request()->header('ua-os', '');
        UserLoginLog::loginLog($params); //登录日志

        $getPersonInfo['uid']   = $uid;
        $getPersonInfo['token'] = $token;

        return $this->getPersonInfo($getPersonInfo);
    }

}
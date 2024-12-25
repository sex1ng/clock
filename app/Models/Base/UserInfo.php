<?php

namespace App\Models\Base;

use App\Services\External\SnowFlake\SnowFlake;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{

    protected $table = 'user_info';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];


    //判断机器人
    const NOT_ROBOT = 0;
    const IS_ROBOT = 1; //群机器人
    const SERVICE_ROBOT = 2; //服务号
    const VEST_ROBOT = 3; //马甲号

    // 用户状态：
    const STATUS_LOCK = -1; //锁定
    const STATUS_DESTROY = 0; //注销
    const STATUS_YES = 1; //正常
    const STATUS_BLACK = 2; //黑名单
    const STATUS_VERSION_BACK_FLAG = 3; //版本回退

    const VIP_STATUS_NOT = 1;       //非会员
    const VIP_STATUS_ING = 2;       //会员
    const VIP_STATUS_EXPIRE = 3;    //会员过期

    const SYMBOL_COMMON = 0;       //普通用户
    const SYMBOL_OFFICIAL = 1;       //官方用户
    const SYMBOL_CERTIFICATION = 2;    //认证用户

    const GUIDE_1 = 1; // 1参照组2实验组
    const GUIDE_2 = 2;

    public static function getUserInfoByOpenid($openid)
    {
        $userInfo = UserInfo::query()->where('openid', $openid)->first();
        if (!$userInfo) {
            $userInfo = new UserInfo();
            $snow_flake = app(SnowFlake::class);
            $userInfo->uid = $snow_flake->uid();
            $userInfo->app_id = config('app.app_id');
            $userInfo->openid = $openid;
            $userInfo->device_id = 0;
            $userInfo->nickname = '用户' . $userInfo->uid;
            $userInfo->avatar = '';
            $userInfo->status = UserInfo::STATUS_YES;
            $userInfo->save();
        }
        return $userInfo;
    }

    public static function getUserInfoByAndroidId($androidId, $appid)
    {
        $userInfo = UserInfo::query()->where('app_id', $appid)->where('android_id', $androidId)->first();
        if (!$userInfo) {
            $userInfo = new UserInfo();
            $snow_flake = app(SnowFlake::class);
            $userInfo->uid = $snow_flake->uid();
            $userInfo->app_id = $appid;
            $userInfo->openid = '';
            $userInfo->android_id = $androidId;
            $userInfo->device_id = 0;
            $userInfo->nickname = '用户' . $userInfo->uid;
            $userInfo->avatar = '';
            $userInfo->status = UserInfo::STATUS_YES;
            $userInfo->save();
        }
        return $userInfo;
    }

}

<?php

namespace App\Services\Business;

use App\Models\Target\Target;
use App\Models\Target\TargetClockinLog;
use Carbon\Carbon;

class TargetService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 添加编辑目标
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function editTarget($params)
    {
        $target_id = $params['target_id'] ?? 0;
        $target = Target::query()->where('target_id', $target_id)->first();
        if (!$target) {
            $target = new Target();
            $target->uid = $params['uid'];
            $target->status = 1;
            $target->start_time = Carbon::now()->toDateTimeString();
            $target->expire_time = Carbon::now()->addYear(10)->toDateTimeString();
        }
        $target->target_name = $params['target_name'];
        $target->target_type = $params['target_type'];
        $target->remind_week = $params['remind_week'];
        $target->remind_hour = $params['remind_hour'];
        $target->app_id = $params['app_id'] ?? BaseService::WEB_ID;
        $target->save();

        $target = Target::query()->where('target_id', $target->target_id)->first();
        // 返回数据处理
        $target = $this->handleTargetInfo($target, $params);
        return $target;
    }

    /**
     * 打卡打卡
     * @param $params
     * @return false|string
     */
    public function clockIn($params)
    {
        $uid = array_get($params, 'uid');
        $target_id = array_get($params, 'target_id');
        if (TargetClockinLog::query()->where('target_id', $target_id)->where('date', date('Ymd'))->exists()) {
            $this->errorMessage = '今日已打过卡';
            return false;
        }

        $target = Target::query()->where('uid', $uid)->where('target_id', $target_id)->first();
        $target->clockin_last_time = Carbon::now()->toDateTimeString();

        //打卡
        $log = Target::getLogBase2($target);
        $log[0] = 1;
        $target->clockin_log = Target::getLogBase64($log);
        $target->save();

        // 记录打卡日志
        $TargetClockinLog = new TargetClockinLog();
        $TargetClockinLog->uid = $uid;
        $TargetClockinLog->target_id = $target_id;
        $TargetClockinLog->date = date('Ymd');
        $TargetClockinLog->content = '';
        $TargetClockinLog->clockin_img = $params['clockin_img'] ?? '';
        $TargetClockinLog->save();
        return $target;
    }


    /**
     * 获取目标列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTargetList($params)
    {
        $limit = $params['limit'] ?? 15;
        $getTargetList = Target::query()->where('uid', $params['uid'])
            ->where('is_delete', 0)
            ->orderByDesc('target_id')
            ->paginate($limit);
        foreach ($getTargetList->items() as &$item) {
            $item = $this->handleTargetInfo($item);
        }

        return $getTargetList;
    }

    /**
     * 处理目标数据
     * @param Target $target
     * @param $params
     * @return Target
     */
    public function handleTargetInfo(Target $target, $params = [])
    {
        $target->clockin_times = TargetClockinLog::query()->where('target_id', $target->target_id)->count();
        return $target;
    }

    /**
     * 删除目标
     * @param $params
     * @return string
     */
    public function delTarget($params)
    {
        $target_id = $params['target_id'] ?? 0;
        $uid = $params['uid'] ?? 0;
        Target::query()->where('uid', $uid)->where('target_id', $target_id)->update(['is_delete' => 1]);
        TargetClockinLog::query()->where('target_id', $target_id)->update(['is_delete' => 1]);
        return '';
    }

}
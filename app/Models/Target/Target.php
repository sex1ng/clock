<?php

namespace App\Models\Target;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Target extends Model {

    protected $table = 'target';

    protected $primaryKey = 'target_id';

    protected $guarded = ['target_id'];

    /** 日志base64转2进制
     * @param Target $target
     * @return array
     */
    public static function getLogBase2(Target $target)
    {
        if ($target->clockin_log == '') {
            $target_start_time = $target->start_time;
            $diffDay = Carbon::today()->diffInDays(Carbon::parse($target_start_time)->startOfDay()) + 1;
            return str_split(str_repeat('0', $diffDay));
        }
        $log = base642bin($target->clockin_log);

        //今天
        $endTime = Carbon::today();
        //最后签到的时间
        $activeEndTime = Carbon::parse($target->clockin_last_time)->timestamp > 0 ? Carbon::parse($target->clockin_last_time)->startOfDay(): Carbon::today();
        //今天还没签到就补0
        if($endTime > $activeEndTime) {
            $log = str_repeat('0', $endTime->diffInDays($activeEndTime)) . $log;
        }
        return str_split($log);
    }

    /** 日志转base64
     * @param $log
     * @return string
     */
    public static function getLogBase64($log)
    {
        if(is_array($log)) {
            $log = implode($log);
        }
        return $log == '' || $log == [] ? '' : bin2Base64(substr($log, 0, 180));
    }

}

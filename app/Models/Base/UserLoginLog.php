<?php

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{

    protected $table = 'user_login_log';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'uid',
        'app_id',
        'channel',
        'version',
        'os',
        'imei',
        'ip',
        'time',
    ];

    public static function loginLog($params = [])
    {
        $loginLog = new UserLoginLog();
        $loginLog->uid = $params['uid'] ?? 0;
        $loginLog->app_id = $params['app_id'] ?? 0;
        $loginLog->version = $params['version'] ?? '';
        $loginLog->imei = $params['imei'] ?? '';
        $loginLog->channel = $params['channel'] ?? '';
        $loginLog->os = $params['os'] ?? '';
        $loginLog->ip = $params['ip'] ?? '';
        $loginLog->time = Carbon::now();
        $loginLog->save();

    }

}

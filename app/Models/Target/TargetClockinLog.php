<?php

namespace App\Models\Target;

use Illuminate\Database\Eloquent\Model;

class TargetClockinLog extends Model {

    protected $table = 'target_clockin_log';

    protected $primaryKey = 'clockin_log_id';

    protected $guarded = ['clockin_log_id'];

}

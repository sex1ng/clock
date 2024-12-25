<?php

namespace App\Models\Bill;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BillLog extends Model {

    protected $table = 'bill_log';

    protected $primaryKey = 'bill_log_id';

    protected $guarded = ['bill_log_id'];

    const TYPE_OUT = 1;
    const TYPE_IN = 2;

}

<?php

namespace App\Models\Diary;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Diary extends Model {

    protected $table = 'diary';

    protected $primaryKey = 'diary_id';

    protected $guarded = ['diary_id'];


}

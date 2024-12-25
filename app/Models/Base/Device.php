<?php

namespace App\Models\Base;
use Illuminate\Database\Eloquent\Model;
class Device extends Model
{

    protected $table = 'user_device';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

}

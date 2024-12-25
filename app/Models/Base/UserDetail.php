<?php
namespace App\Models\Base;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{

    protected $table = 'user_detail';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'uid',
        'signature',
        'gender',
        'birth',
        'true_name',
        'id_card',
        'country',
        'province',
        'city',
        'area',
    ];

    // 性别：未知
    const GENDER_NULL = 0;
    // 性别：男
    const GENDER_MALE = 1;
    // 性别：女
    const GENDER_FEMALE = 2;

}

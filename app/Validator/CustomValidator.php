<?php

namespace App\Validator;

use Illuminate\Validation\Validator;

class CustomValidator extends Validator
{

    /**
     * 姓名验证
     */
    public function validateName($attribute, $value, $parameters)
    {
        if (preg_match('/^[\x{4e00}-\x{9fa5}·]{2,20}$/u', $value)) {
            return true;
        }

        return false;
    }

}
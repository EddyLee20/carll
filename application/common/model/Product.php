<?php

namespace app\common\model;

use think\Model;

class Product extends Model
{
    //

    /*public static function getProductByMap($map, $field='*')
    {
        $res = self::where($map)->field($field)->find();
        if(!empty($res) && is_object($res)){
            return $res->toArray();
        }
        return $res;
    }*/
}

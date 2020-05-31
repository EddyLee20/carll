<?php

namespace app\common\model;

use think\Model;

class Illegal extends Model
{
    protected $name = 'user_illegal';
    //自动写入
    protected $auto = [];
    protected $insert = ['status' => 0];
    protected $update = [];
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
    protected $updateTime = 'update_time';

    public static function calcIllegalByMap($map)
    {
        //违章次数
        $number = self::where($map)->count();
        //违章金额
        $amount = self::where($map)->sum('illegal_amount') ?? 0;
        //违章分数
        $point = self::where($map)->sum('illegal_point') ?? 0;

        return compact('number', 'amount', 'point');
    }
}

<?php

namespace app\common\model;

use think\Model;

class Users extends BaseModel
{
    protected $name = 'user';
    //自动写入
    protected $auto = [];
    protected $insert = ['status' => 1,'id_type'=>1];
    protected $update = [];
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = 'update_time';

    public static function getUserByMap($map, $field='*')
    {
        $res = self::where($map)->field($field)->find();
        if(!empty($res) && is_object($res)){
            return $res->toArray();
        }
        return $res;
    }

    public static function getCacheByTel($telphone)
    {

    }

}

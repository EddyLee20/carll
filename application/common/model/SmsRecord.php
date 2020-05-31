<?php

namespace app\common\model;

use think\Model;

class SmsRecord extends Model
{
    protected $name = 'sms_record';
    //自动写入
    protected $auto = [];
    protected $insert = ['status' => 1];
    protected $update = [];
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = false;

    /**
     * 统计短信发送记录
     * @param $telphone
     * @return int|string|\think\db\Query
     */
    public function countRecordByDay($telphone)
    {
        $start = strtotime(date('Y-m-d'));
        $end = $start + 86400;
        $map = [
            'telphone'  =>  $telphone,
            'status'    =>  1,
            'add_time'  =>  ['between', [$start, $end]],
        ];
        $map['add_time'] = ['between', [$start, $end]];
        $res = self::where($map)->count();
        return $res;
    }

}

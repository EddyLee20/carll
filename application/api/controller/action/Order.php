<?php
/**
 * Created by PhpStorm.
 * User: Eddy
 * Date: 2020/5/31
 * Time: 21:56
 */

namespace app\api\controller\action;


use app\common\traits\ApiHelper;
use think\Request;

class Order
{
    public $User,$request;
    public function __construct($base = '')
    {
        if(empty($base)) return ApiHelper::output(20000);
        $this->User = $base;
        $this->request = Request::instance();
    }

    public function save()
    {
        $param = $this->request->param();
    }

}
<?php


namespace app\api\controller;


use app\common\model\Users;
use app\common\traits\ApiHelper;
use think\Cache;
use think\Config;
use think\Request;

class Base extends \think\Controller
{
    public $action;
    protected $User = [];
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function _initialize()
    {
        $this->action = $this->request->param('action', null);
        /*$token = $this->request->param('token', null);
        //登录验证
        $info = Cache::get('device_'.$token);
        if(empty($info)) return ApiHelper::output(20000, '登录失效，请重新登录');
        //查询用户信息
        $userInfo = Users::getUserByMap(['user_id'=>$info['user_id']], 'user_id,username,car_licenc,telphone,img,status,device_token,device_limit_time');
        if(empty($userInfo)) return ApiHelper::output(20000, '用户信息异常');
        if($this->checkToken($userInfo, $token) === false) return ApiHelper::output(20000, '登录信息异常');

        $this->User = array_diff($userInfo, ['device_token','device_limit_time']);*/
    }

    /**
     * 公共访问入口
     * @return mixed
     */
    public function index()
    {
        $class = substr($this->action,0,strpos($this->action, '_'));
        $func = substr($this->action,strpos($this->action, '_')+1);
        $resp = (new Driver(['class'=>$class,'User'=>['user_id'=>1]]))->getFunc($func);
        return $resp;
    }

    /**
     * 检查用户token是否正确
     * @param $userInfo
     * @param $token
     * @return bool
     */
    public function checkToken($userInfo, $token) {

        if($userInfo['device_token'] != $token) return false;
        $time = time();
        if($time <= $userInfo['device_limit_time']){
            Cache::set('device_'.$token, $userInfo, Config::get('token_limit'));
            return true;
        }else{
            Cache::set('device_'.$token, null);
            return false;
        }
    }

    /**
     * 空操作
     * @param $name
     * @return array
     */
    public function _empty($name)
    {
        return ApiHelper::output(20000, '系统异常:'.$name."请求不存在");
    }
}
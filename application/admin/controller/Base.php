<?php


namespace app\admin\controller;

use app\common\traits\ApiHelper;
use think\Cache;
use think\Controller;
use think\Request;

class Base extends Controller
{
    public $action;
    public $token;
    public $uid;
    public $common_set = array();//公共配置
    public $User;
    public $Log;
    protected $baseInfo;
    protected $common_group = false;
    //通用接口
    public $common_action = array(
        'logout','get_provinces','get_citys'
    );

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function _initialize()
    {
//        $this->baseInfo = ApiHelper::output(10000, ['user_view_new'], $this->getLogoutUrl(), 'json');
        $this->request = Request::instance();
        $this->action = $this->request->param('action',null);
        $this->token = $this->request->param('token',null);
        $this->uid = $this->request->param('uid',null);
        /*$check = $this->check_token($this->uid, $this->token);
        if(!$this->uid){
            $this->baseInfo = ApiHelper::output(30000, '登录信息异常，请重新登录', $this->getLoginUrl(), 'json');
        }
        if (!$check) {
            $this->baseInfo = ApiHelper::output(30000, '登录状态已过期，请重新登录', $this->getLoginUrl(), 'json');
        }*/

        /*$u_data = Cache::get($this->uid.'_uinfo');
        $group_id = $u_data['group_id'];
        $condition = array('id' => $group_id);
        $this->User = new User();
        $group_info = $this->User->getGroupInfo($condition);
        $view_ids = $group_info['view_ids'];
        $view_module_ids = $group_info['view_module_ids'];
        $user_view_arr = array();
        if(!empty($view_ids)){
            $condition = 'id in ('.$view_ids.')';
            $user_view_list = $this->User->getUserGroupViewApi($condition);
            if(!empty($user_view_list)){
                foreach($user_view_list as $key=>$val){
                    $val['api'] = trim($val['api'],',');
                    $_api = explode(',',$val['api']);
                    if(!empty($_api)){
                        foreach($_api as $key=>$val){
                            array_push($user_view_arr,$val);
                        }
                    }
                }
            }
        }
        if(!empty($view_module_ids)){
            $condition = 'id in ('.$view_module_ids.')';
            $user_view_list = $this->User->getUserModuleViewApi($condition);
            if(!empty($user_view_list)){
                foreach($user_view_list as $key=>$val){
                    $val['api'] = trim($val['api'],',');
                    $_api = explode(',',$val['api']);
                    if(!empty($_api)){
                        foreach($_api as $key=>$val){
                            array_push($user_view_arr,$val);
                        }
                    }
                }
            }
        }
        $user_view_new = array_merge($this->common_action,$user_view_arr);
        $request = Request::instance();
        $action_name = $request->action();
        if(!in_array($this->action, $user_view_new) && !in_array($action_name, $user_view_new)){
            return ApiHelper::output(30000, "信息错误:您无权操作该模块",'','','json');
        }

        //获取参数
        $param = $request->param();
        if(!key_exists('data', $param)){
            return ApiHelper::output(30000, "信息错误:请求参数有误",'','','json');
        }

        //日志类初始化
        LogHandle::set_config(['table'=>['user'=>$u_data, 'module'=>$request->pathinfo(), 'action'=>$this->action, 'ip'=>$request->ip()]]);
        $this->Log = LogHandle::init('table');
        //超管权限标识
        if($u_data['group_id'] == config('common_user.group_id')){
            $this->common_group = true;
        }

        $this->param = $param['data'];*/

    }

    /*检查用户token是否正确*/
    public function check_token($uid,$token) {
        $u_data = Cache::get($uid.'_uinfo');
        if(empty($u_data)){
            return false;
        }
        if($token != $u_data['token']){
            return false;
        }
        $time = time();
        $login_time = Cache::get($uid.'_login_time');
        if(!empty($login_time)){
            $minute = floor(($time-$login_time)%86400/60);
            if($minute > 30){
                Cache::set($uid.'_uinfo',null);
                Cache::set($uid.'_login_time',null);
                return false;
            }else{
                Cache::set($uid.'_uinfo',$u_data,1800);
                Cache::set($uid.'_login_time',$time,1800);
                return true;
            }
        }else{
            return true;
        }
    }

    /**
     * 空操作
     * @param $name
     * @return array
     */
    public function _empty($name)
    {
        return ApiHelper::output(20000, '系统异常:'.$name."请求不存在",'','','json');
    }

    public function checkRole($uid, $roles) {
        $u_data = Cache::get($uid.'_uinfo');
        if(!in_array($u_data['group_id'], $roles)) {
            return false;
        }
        return true;
    }

    public function getLoginUrl()
    {
        return $this->request->domain().'/admin/passport/login';
    }

    public function getLogoutUrl()
    {
        return $this->request->domain().'/admin/user/logout';
    }
}
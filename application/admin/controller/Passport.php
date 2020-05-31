<?php


namespace app\admin\controller;


use app\common\model\AdminUser;
use app\common\traits\ApiHelper;
use think\Cache;
use think\Request;

class Passport extends \think\Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }


    /**
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function login(){
        if($this->request->isPost()){
            return $this->doLogin();
        }
        return $this->fetch();
    }

    /**
     * 用户登录接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function doLogin(){
        $url = $this->request->domain().'/admin/index';
        $username = $this->request->param('username',null);
        $password = $this->request->param('password',null);
        $User = new AdminUser();
        if (!empty($username) && !empty($password)) {
            $condition = array('username' => $username, 'del_flag' => 0);
            $info = $User->getUserInfo($condition);
            $one_login = ApiHelper::getPassword();
            if($info === FALSE){
                return ApiHelper::output(20000, "db error");
            } elseif (empty($info)) {
                return ApiHelper::output(30000, "用户名不存在！");
            } else {
                if ($info['password'] != ApiHelper::getPassword($password, $info['salt'])['password']) {
                    return ApiHelper::output(30000, "密码错误，请重新输入！");
                } elseif ($info['expired_time'] < time()) {
                    return ApiHelper::output(30000, "该用户已过期，请联系管理员！");
                }elseif($info['password'] == $one_login['password']){

                    $r_data = array(
                        'code' => 7,
                        'message' => '该用户首次登录系统，请先修改密码！',
                        'uid' => $info['id'],
                        'user' => array(
                            'uid' => $info['id'],
                            'nickname' => $info['nickname'],
                            'remark'    =>  $info['remark'],
                            'expired_time' => $info['expired_time']
                        )
                    );
                    return ApiHelper::output(10000, $r_data, compact('url'));
                } else {
                    $token = ApiHelper::getAuth($info['id']);
                    /*$condition = array('id' => $info['group_id']);
                    $group_info = $User->getGroupInfo($condition);
                    $role = array();
                    if(!empty($group_info)){
                        if(empty($group_info['view_ids'])){
                            return ApiHelper::output(30000, '请联系管理员分配阅读权限');
                        }
                        $role = $User->getGroupViewInfo($group_info['view_ids']);
                        if(!empty($role)){
                            foreach($role as $key=>$val){
                                $role[$key]['chlid'] = $User->getViewChlidByViewId($val['view_id'],$group_info['view_ids']);
                            }
                            foreach($role as $key=>$val){
                                $role[$key]['method'] = $User->getModuleFromViewId($val['view_id'],$group_info['view_module_ids']);
                                if(!empty($val['chlid'])){
                                    foreach($val['chlid'] as $key2=>$val2){
                                        $role[$key]['chlid'][$key2]['method'] = $User->getModuleFromViewId($val2['view_id'],$group_info['view_module_ids']);
                                    }
                                }
                            }
                        }
                    }*/
                    $r_data = array(
                        'code' => 1,
                        'message' => '登录成功',
                        'uid' => $info['id'],
                        'token' => $token,
                        'user' => array(
                            'uid' => $info['id'],
                            'nickname' => $info['nickname'],
                            'remark'    =>  $info['remark'],
                            'expired_time' => $info['expired_time'],
//                            'group_name'    =>  $group_info['group_name']
                        ),
//                        'role' => $role
                    );
                    $_user['id'] = $info['id'];
                    $_user['last_login_time'] = time();
                    $User->update($_user);
                    $login_time = time();
                    $set_data = array(
                        'user_id' => $info['id'],
                        'username' => $info['username'],
                        'nickname' => $info['nickname'],
                        'login-time' => time(),
                        'token' => $token,
                        'group_id' => $info['group_id'],
                        'expired_time' => $info['expired_time']
                    );
                    Cache::set($info['id'].'_uinfo',$set_data,3600);
                    Cache::set($info['id'].'_login_time',$login_time,3600);
                    return ApiHelper::output(10000, $r_data, compact('url'));
                }
            }
        }else{
            return ApiHelper::output(30000, '请输入用户名和密码');
        }

    }

    /**
     * 密码重置接口 new
     * @param  int    $id           用户id            必填
     * @param  string $old_password     原密码            必填
     * @param  string $password     新密码            必填
     * @param  string $ck_password 确认密码          必填
     * @return @return array
     * @return string message       反馈信息
     */
    public function passwordReset(){

        $id = $this->request->param('uid',null);
        $password = $this->request->param('password',null);
        $pass_old = $this->request->param('old_password',null);
        $pass_confirm = $this->request->param('ck_password',null);
        if(!$id || !$password || !$pass_old || !$pass_confirm){
            return ApiHelper::output(30000, '缺少传入参数');
        }
        $User = new AdminUser();
        if(! $password){
            return ApiHelper::output(30000, '缺少传入参数');
        }
        if($password != $pass_confirm){
            return ApiHelper::output(30000, '两次密码输入不一致');
        }
        $condition = array('id' => $id, 'status' => 1);
        $info = $User->getUserInfo($condition);
        if($info['password'] != ApiHelper::getPassword($pass_old, $info['salt'])['password']){
            return ApiHelper::output(30000, '原密码不正确');
        }
        $check = ApiHelper::checkFormat(7, $password);
        if(!$check){
            return ApiHelper::output(30000, '密码格式错误');
        }
        $data['id'] = $id;
        $getPass = ApiHelper::getPassword($password);
        $data['password'] = $getPass['password'];
        $data['salt'] = $getPass['salt'];
        $result = $User->update($data);
        if ($result === FALSE) {
            return ApiHelper::output(20000, '数据库出错，操作失败');
        }
        return ApiHelper::output(10000, '密码重置成功');
    }

}
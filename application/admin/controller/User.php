<?php


namespace app\admin\controller;


use app\common\model\AdminUser;
use app\common\traits\ApiHelper;
use app\common\validate\AdminUser as userValidate;
use think\Cache;
use think\Request;

class User extends Base
{
    public $validate;
    public $User;
    public function __construct()
    {
        parent::__construct();
        $this->validate = new userValidate();
        $this->User = new AdminUser();
    }

    public function lists()
    {
        return $this->fetch('user/lists');
    }

    /**
     * ajax请求路由
     * @return array
     * @throws \think\exception\DbException
     */
    public function index()
    {
        switch ($this->action){
            case 'user_logout':
                $result = $this->logout();
                break;
            case 'user_edit_pwd':
                $result = $this->edit_pwd();
                break;
            default:
                $result = ApiHelper::output(20000);
                break;
        }
        return $result;
    }

    /**
     * 用户退出登录接口
     * @param  string $uid      用户id
     * @param  string $token    用户token
     * @return
     */
    public function logout(){

        $uid = $this->request->param('uid',null);
        if(! $uid){
            return ApiHelper::output(30000, "缺少用户id");
        }else{
            Cache::set($uid.'_uinfo',null);
        }
        return ApiHelper::output(10000, "退出成功");
    }

    /**
     * 修改用户密码接口
     * @param  string $uid      用户id            必填
     * @param  string $token    用户token         必填
     * @param  string $id       待操作用户id      必填
     * @param  string $old_password 用户密码id      必填
     * @param  string $password 用户密码id      必填
     * @param  string $ck_password 确认密码       必填
     * @return mixed
     */
    public function edit_pwd() {
        return ApiHelper::output(10000, $this->param);
        $result = $this->validate->scene('pwdEdit')->check($this->param);
        //数据验证错误
        if(!$result){
            return ApiHelper::output(30000, $this->validate->getError());
        }
        $condition = array('id' => $this->param['uid'], 'status' => 1,'del_flag' => 0);
        $info = $this->User->getUserInfo($condition);
        if($info['password'] != ApiHelper::getPassword($this->param['old_password'], $info['salt'])['password']) {
            return ApiHelper::output(30000, '原密码错误');
        }
        $getPass = ApiHelper::getPassword($this->param['password']);
        $update = ['password'=>$getPass['password'], 'salt'=>$getPass['salt'], 'id'=> $this->param['uid']];
        $result = $this->User->save($update, ['id'=>$this->param['uid']]);
        if ($result === FALSE) {
            return ApiHelper::output(20000, '数据库出错，无法修改');
        }
        return ApiHelper::output(10000, '修改成功！');
    }

    /**
     * 密码重置接口
     * @param  int    $uid          用户id            必填
     * @param  string $token        用户token         必填
     * @param  int    $id           用户id            必填
     * @param  string $pass_old     原密码            选填
     * @param  string $pass_confirm 确认密码          选填
     * @return mixed
     */
    public function password_reset(){
        $result = $this -> validate ->scene('pwdReset')->check($this->param);
        if(!$result){
            return ApiHelper::output(30000, $this -> validate->getError());
        }
        $this->Log->info($this->param); // 操作日志
        $getPass = ApiHelper::getPassword();
        $data['password'] = $getPass['password'];
        $data['salt'] = $getPass['salt'];
        $result = $this->User->save($data,$this->param);
        if ($result === FALSE) {
            $this->Log->info('数据库出错，操作失败'); // 操作日志
            return ApiHelper::output(20000,'数据库出错，操作失败');
        }
        $this->Log->info('重置密码成功'); // 操作日志
        return ApiHelper::output(10000,'重置密码成功');
    }
}
<?php

namespace app\common\model;

use think\Db;
use think\Model;

class AdminUser extends Model
{
    protected $auto = [];
    protected $insert = ['status' => 1,'del_flag'=>0, 'password' => 'e10adc3949ba59abbe56e057f20f883e'];
    protected $update = [];

    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = 'update_time';

    public function setExpiredTimeAttr($value)
    {
        return strtotime($value);
    }
    public function getExpiredTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }
    public function getDisabledTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }

    /**
     * 获取user单条数据
     * @param $condition
     * @return mixed
     */
    public function getUserInfo($condition)
    {
        $user = Db::name('admin_user')->field('id,username,nickname,password,expired_time,group_id,salt,remark')->where($condition)->find();
        return $user;
    }



    /**
     * 获取不同组对应的
     * @param string $view_ids
     * @return Array
     */

    public function getGroupViewInfo($view_ids)
    {
        $group = db('user_group_view')
            ->field('id as view_id,view_name,remark')
            ->where("id in (".$view_ids.") and pid = 0")
            ->order('_asc asc')
            ->select();
        return $group;
    }

    /**
     * 获取group 信息 在登陆与base中有用
     */
    public function getGroupInfo($condition)
    {
        $group = db('user_group')
            ->field('id,view_ids,view_module_ids,group_name')
            ->where($condition)
            ->find();
        return $group;
    }

    /**
     * 根据上级视图id与用户的视图id集,获取二级视图信息
     */
    public function getViewChlidByViewId($view_id,$ids= []) {
        if($ids == []) {
            $where = "pid = {$view_id}";
        } else {
            $where = "pid = {$view_id} and id in ({$ids})";
        }

        $role = db('User_group_view')
            ->field('id as view_id,view_name,remark')
            ->where($where)
            ->order('_asc asc')
            ->select();
        return empty($role)?'':$role;
    }

    /**
     * 根据二级视图id与用户方法id集,获取对应操作方法
     */
    public function getModuleFromViewId($view_id,$ids=[]){
        if(!empty($ids)){
            $list = db('User_view_module')
                ->field('id as method_id,module_name as method_name,remark')
                ->where("view_id = {$view_id} and id in({$ids})")
                ->order('_asc asc')
                ->select();
        }else{
            $list = db('User_view_module')
                ->field('id as method_id,module_name as method_name,remark')
                ->where("view_id = {$view_id}")
                ->order('_asc asc')
                ->select();
        }
        return empty($list)?'':$list;
    }

    /**
     * 获取所有的顶级view
     */
    public function getAllParentView()
    {
        $role_all = db('User_group_view')
            ->field('id as view_id,view_name,remark')
            ->where("pid = 0")
            ->order('_asc asc')
            ->select();
        return $role_all;
    }

    /**
     * 获取user_group_view中的api权限值
     */
    public function getUserGroupViewApi($condition){
        $list = db('user_group_view')
            ->where($condition)
            ->field('api')
            ->select();
        return empty($list)?'':$list;
    }

    /**
     * 获取user_group_view中的api权限值
     */
    public function getUserModuleViewApi($condition){
        $list = db('user_view_module')
            ->where($condition)
            ->field('api')
            ->select();
        return empty($list)?'':$list;
    }
    /**
     * 查询用户列表
     */
    public function getUserList($where, $page, $page_size = 20) {
        $config = ['paginate'=>$page_size, 'page'=>$page];
        $list = Db::name('admin_user')->alias('u')
            ->join('user_group g','u.group_id=g.id','left')
            ->field('u.id,
    				nickname,
    				username,
                    u.email,                  
                    u.remarks,
                    g.group_name,
    				user_phone,
    				group_id,
    				status,
    				u.address,
                    expired_time,
                    last_login_time')
            ->where($where)
            ->order('u.id desc')
            ->paginate($config);
        $info = $list->toArray();
        return empty($info)?'':$info;
    }

    /**
     * 确认用户名是否重复
     */
    public function checkUsername($username, $uid) {
        $result = Db::name('admin_user')->where('username = "'.$username.'"')->find();
        if ($result === FALSE) {
            return true;
        } elseif (empty($result)) {
            return false;
        } else {
            if($result['id'] == $uid){
                return false;
            }

            return true;
        }
    }

    /**
     * 获取uid下绑定的分账方
     */
    public function getDistributionUserList($uid) {
        $list = db('user_distribution as u')
            ->join('distribution_group_user d','u.group_uid = d.id')
            ->where('u.user_id = "'.$uid.'"')
            ->field('d.group_username,u.group_uid')
            ->select();
        return empty($list)?'':$list;
    }

    /**
     * 确认uid与分账方是否有绑定
     */
    public function checkDistributionUser($uid, $did) {
        $list = db('user_distribution as u')
            ->where('u.user_id = "'.$uid.'" and u.group_uid = "'.$did.'"')
            ->find();
        if (empty($list)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 绑定用户组的视图权限
     * @param int $gid 用户组id
     * @param array $view_ids 要绑定的视图id数组
     */
    public function saveGroupViewByIds($gid, $view_ids)
    {
        Db::startTrans();
        try {
            $view_str = implode(',', $view_ids);
            $where['id'] = array('in',$view_str);
            $view_info = self::where($where)
                -> field('pid')
                ->group('pid')
                -> select();
            $first_ids = array();
            if(empty($view_info)){
                return false;
            }
            foreach($view_info as $key=>$val){
                $val['pid'] <> 0 ? $first_ids[] = $val['pid'] : '';
            }
            $first_ids = empty($first_ids) ? '' : implode(',',$first_ids);
            $view_str = $first_ids == '' ? $view_str : $first_ids.','.$view_str;
            $view_ids = explode(',',$view_str);
            $view_ids = array_unique($view_ids);
            $view_ids = implode(',',$view_ids);
            $data['id'] = $gid;
            $data['view_ids'] = $view_ids;
            db('User_group')->update($data);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e){
            Db::rollback();
            return false;
        }
    }


    /**
     * 查询用户组对应的列表
     */
    public function getGroupUserList($gid) {
        $list = Db::name('admin_user')->where (array('del_flag'=>0, 'group_id'=> $gid))->field('username, id')->select();
        return empty($list)?'':$list;
    }

}

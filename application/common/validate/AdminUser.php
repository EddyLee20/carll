<?php

namespace app\common\validate;
use \think\Validate;

class AdminUser extends Validate
{
    protected $rule =   [
        'id'            => 'require|number|max:11',
        'username'     => 'require|chsAlphaNum|max:20|unique:user,username',
        'password'    => 'require|max:100',
        'ck_password'    => 'require|max:100|confirm:password|different:old_password',
        'old_password'    => 'require|max:100',
        'nickname'    => 'require|max:255|alphaDash|unique:user,nickname',
        'user_phone'    => 'require|max:15',
        'email'    => 'require|email|max:50',
        'address'    => 'require|max:255',
        'add_time'    => 'dateFormat',
        'del_flag'    => 'require|in:0,1',
        'status'    => 'require|in:0,1',
        'remarks'    => 'require|max:50',
        'group_id'    => 'require|number',
        'last_login_time'    => 'dateFormat',
        'disabled_reason'    => 'max:50',
        'disabled_time'    => 'dateFormat:Y-m-d',
        'expired_time'    => 'require|dateFormat:Y-m-d',
        'add_user_id'    => 'require|number|max:10',
        'update_time'    => 'dateFormat',
        'share_ids'    => 'require|array',
        'page'    => 'require|number',
        'page_size'    => 'require|number',
    ];

    protected $message  =   [
        'id.number'             => '用户ID必须为数字类型',
        'id.max'                => '用户ID只能在1-8之间',
        'id.require'                => '用户ID必须存在',
        'username.require'     => '用户名必须',
        'username.chsAlphaNum'     => '用户名必须中文，英文和数字',
        'username.max'     => '用户名必须小于20个字符',
        'username.unique'     => '用户名已存在',
        'password.require'     => '密码必须',
        'password.max'     => '密码必须小于100个字符',
        'nickname.require'     => '昵称必须',
        'nickname.max'     => '昵称必须小于255个字符',
        'nickname.alphaDash'     => '昵称必须字母和数字，下划线_及破折号-',
        'nickname.unique'     => '昵称已存在',
        'user_phone.require'     => '手机号码必须',
        'user_phone.max'     => '手机号码必须小于15个字符',
        'email.require'     => '邮箱必须',
        'email.email'     => '邮箱必须为邮箱格式',
        'email.max'     => '邮箱必须小于50个字符',
        'address.email'     => '地址必须',
        'address.max'     => '地址必须小于255个字符',
        'status.require'     => '状态必须',
        'status.in'     => '状态必须为0或者1',
        'remarks.require'     => '备注必须',
        'remarks.max'     => '备注必须在50个字符以内',
        'group_id.require'     => '用户组必须',
        'group_id.number'     => '用户组必须为数字',
        'expired_time.require'     => '失效时间必须填写',
        'expired_time.dateFormat'     => '失效时间必须为日期格式',
        'disabled_time.dateFormat'     => '禁用时间必须为日期格式',
        'old_password.require'     => '请输入原密码',
        'old_password.max'     => '原密码长度在100字符以内',
        'ck_password.max'     => '密码长度在100字符以内',
        'ck_password.confirm'     => '确认密码与密码不同',
        'ck_password.different'     => '原密码与密码相同',
        'password.require'     => '密码必须输入',
        'disabled_reason.max'     => '禁用原因长度在50字符以内',
        'share_ids.require'     => '需要绑定的分账方必须输入',
        'share_ids.array'     => '需要绑定的分账方为数组结构',
        'page.require'     => '当前页必须存在',
        'page_size.require'     => '每页多少条必须存在',
    ];

    public $scene = [
        'add'   =>  ['username','nickname','user_phone','expired_time','address', 'group_id','email','remarks'],
        'edit'  =>  ['id','username','nickname','user_phone','expired_time','address', 'group_id','email','remarks'],
        'delete'  =>  ['id'],
        'pwdReset'  =>  ['id'],
        'pwdEdit'  =>  ['id','old_password','password','ck_password'],
        'shareBind'  =>  ['id','share_ids'],
        'lists'  =>  ['page','page_size'],
        'enable'  =>  ['id','disabled_reason','status'],
    ];
}
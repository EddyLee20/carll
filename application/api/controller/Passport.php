<?php

namespace app\api\controller;

use app\common\model\AdminUser;
use app\common\model\Setting;
use app\common\model\SmsRecord;
use app\common\model\Users;
use app\common\traits\ApiHelper;
use app\common\library\sms\Driver as SmsDriver;
use app\common\traits\IdRandom;
use think\Cache;
use think\Config;
use think\Exception;
use think\Request;

class Passport extends \think\Controller
{
    public $userValidate;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userValidate = new \app\common\validate\User();
    }

    /**
     * 发送短信
     */
    public function sendSms()
    {
        $telphone = $this->request->param('telphone',null);
        //查询短信是否过期
        if(!empty(Cache::get('smscode_'.$telphone))){
            return ApiHelper::output(20001, '请勿重复获取验证码');
        }
        //统计当天发送条数
        $smsRecord = new SmsRecord();
        if($smsRecord->countRecordByDay($telphone) > 10){
            return ApiHelper::output(20001, '您今日发送短信条数超过最大值');
        }
        try {
            $smsConfig = Setting::getItem('sms');
            $smsCode = getSmsCode();

            $resp = (new SmsDriver($smsConfig))->sendSms("86", $telphone, 'login', [$smsCode, 2]);//var_dump($resp);exit;
            if($resp['result'] != 0){
                throw new Exception('短信发送失败');
            }
            //短信缓存
            Cache::set('smscode_' . $telphone, $smsCode, 3600);
            //发送记录
            $smsRecord->save(['telphone'=>$telphone, 'sms_type'=>'login', 'sms_code'=>$smsCode]);
            return ApiHelper::output(10000, '短信发送成功');
        }catch (Exception $e){
            return ApiHelper::output(20000, $e->getMessage());
        }
    }

    /**
     * 短信登录
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function login(){
        $param = $this->request->param();
        $result = $this->userValidate->scene('login')->check($param);
        //数据验证错误
        if(!$result){
            return ApiHelper::output(20002, $this->userValidate->getError());
        }
        $telphone = $param['telphone'];
        //短信验证
        $tempCode = Cache::get('smscode_'.$telphone);
        if(empty($tempCode)){
            return ApiHelper::output(20001, '您的短信验证码已过期，请重新获取');
        }elseif($tempCode != $param['sms_code']){
            return ApiHelper::output(20001, '您的短信验证码有误');
        }

        try {
            $userModel = new Users();
            $userInfo = $userModel::getUserByMap(['username' => $param['username'], 'car_licenc' => $param['car_licenc']], 'user_id,status');
            if($userInfo['status'] == 0){
                throw new Exception('当前账号已禁用,请联系平台运营人员');
            }
            //首次登录
            if (empty($userInfo)) {
                $param['id_number'] = IdRandom::getID();
                $resU = $userModel->allowField(array_merge($this->userValidate->scene['login'], ['id_number']))->save($param);
                if (!$resU) {
                    throw new Exception('登录失败');
                }
                $userInfo['user_id'] = $userModel->user_id;
            }
            $token = ApiHelper::getAuth($userInfo['user_id']);
            $time = time();
            $cacheTime = Config::get('token_limit');
            $upData = [
                'user_id'       =>  $userInfo['user_id'],
                'device_token'  => $token,
                'device_limit_time' => $time + $cacheTime
            ];
            $resUp = $userModel->isUpdate(true)->save($upData);
            if (!$resUp) {
                throw new Exception('登录失败');
            }
            //登录缓存
            Cache::set('device_'.$token, $userInfo, $cacheTime);
//            Cache::set('login_time'.$token, $time, $cacheTime);

            return ApiHelper::output(10000, compact('token'));
        }catch (Exception $e){
            return ApiHelper::output(20000, $e->getMessage());
        }
    }

}
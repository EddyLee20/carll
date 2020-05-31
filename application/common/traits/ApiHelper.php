<?php

namespace app\common\traits;

class ApiHelper
{
    /**
     * 默认分页大小
     */
    public static $pagesize = 20;

    /**
     * 格式化输出
     * @param int $resp_code
     * @param string $resp_msg
     * @param array $param 跳转链接：url 分页标签：pagination
     * @param string $format
     * @return array
     */
    public static function output($resp_code = 0, $resp_msg = '', $param = [], $format = 'json')
    {
        $return = array();

        $resp_code = intval($resp_code);

        if($resp_code == 10000){
            // success
            $return['resp_code'] = 10000;
            $return['resp_msg'] = self::getRespMsg($resp_code);
            if(is_array($resp_msg)){
                // 数据数组
                if((isset($resp_msg[0]) && count($resp_msg) != count($resp_msg, 1)) || $resp_msg === array() ){
                    // 是二维数组（数据列表）
                    $return['data'] = array(
                        'datalist'	=>	$resp_msg
                    );
                }else{
                    // 不是二维数组
                    $return['data'] = $resp_msg;
                }
            }elseif ($resp_msg === '') {
                // 未传参数或传空字符串
                $return['data'] = '';
            }else{
                // $resp_msg是不为空的字符串或其他数据
                $return['resp_msg'] = $resp_msg;
                $return['data'] = '';
            }

        }else{
            // error
            $return['resp_code'] = $resp_code;
            if(is_string($resp_msg)){
                $return['resp_msg'] = $resp_msg ? $resp_msg : self::getRespMsg(20000);
                $return['data'] = '';
            }else{
                // $resp_msg不是字符串
                $return['resp_msg'] = self::getRespMsg(40000);
                $return['data'] = is_array($resp_msg) ? $resp_msg : '';
            }

        }
        //合并输出数据
        if(!empty($param) && is_array($param)){
            $return['data'] = array_merge($return['data'], $param);
        }
        //输出数据
        if($format == 'json'){
            // 以json格式输出
            if(!headers_sent()){
                @header("Content-type:application/json");
            }

            echo json_encode($return);
            exit();
        }else{
            // 以数组形式返回
            return $return;
        }
    }

    /**
     * 预定义状态码
     * @param int $resp_code
     * @return bool|mixed
     */
    protected static function getRespMsg($resp_code = 0)
    {

        if($resp_code == 0) return false;
        $array = array(
            10000   =>  'SUCCESS',      //成功返回
            20000   =>  'SYSTEM_ERROR', //系统错误返回
            30000   =>  'HANDLE_ERROR', //信息错误返回
            40000   =>  'ERROR',        //未知错误
        );
        return $array[$resp_code];
    }

    /**
     * 格式化分页
     * @param $page_info
     * @param int $page
     * @return array|bool
     */
    public static function pagination($page_info, $page=0)
    {
        if (empty($page_info) && !is_array($page_info)){
            return false;
        }
        $pagination = array(
            'total'	        =>	$page_info['total'],
            'page'			=>	intval($page),
            'page_size'		=>	$page_info['per_page'],
            'page_count'	=>	(int) ceil($page_info['total'] / $page_info['per_page']),
        );
        return $pagination;
    }

    /**
     * 获取标识access_key
     * @param $user_id
     * @param $label_rule
     * @return bool|string
     */
    public static function getAuth($user_id)
    {
        if(empty($user_id)){
            return false;
        }
        $str = md5(uniqid(md5(microtime(true)), true));
        $access_token = sha1($str.$user_id);
        return $access_token;
    }

    /**
     * sign签名验证
     * @param array $data
     * @param string $access_key
     * @return bool
     */
    public static function checkSign($data = [], $access_key='')
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $str = self::getDataToStr($data);
        $strKey = ltrim($str, '&').'&access_key='.$access_key;
        $verifySign = strtoupper(md5($strKey));
        if($verifySign == $sign){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 签名数据转换
     * @param $data
     * @return string
     */
    public static function getDataToStr($data)
    {
        ksort($data);
        $str = '';
        foreach ($data as $k => $v){
            if (empty($v) || $v == ''){
                continue;
            }
            if(is_array($v)){
                ksort($v);
                $value = json_encode($v);
            }else{
                $value = trim($v);
            }

            $str .= '&'.$k.'='.$value;
        }
        return $str;
    }

    /**
     * 正则验证
     * @param $type
     * type = 1,用户名：不超过10个字节,只能数字、字母、中文或下划线
     * type = 2,登录账号：不超过10个字节，只能英文、数字和下划线
     * type = 3,address:不超过50字节
     * type = 4,备注：不超过200个字节
     * type = 5,email
     * type = 6,手机号
     * type = 7,登录密码,只能输入字母、数字、符号且密码需包含大小写字母和数字
     * @param $str
     * @return bool
     */
    public static function checkFormat($type,$str)
    {
        if($type == 1){
            if(preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u',$str)){
                $len = mb_strlen($str,'utf-8');
                if($len > 10){
                    return false;
                }
                return true;
            }else{
                return false;
            }
        }elseif($type == 2){
            if(preg_match('/^[0-9a-zA-Z_]+$/u',$str)){
                $len = mb_strlen($str,'utf-8');
                if($len > 10){
                    return false;
                }
                return true;
            }else{
                return false;
            }
        }elseif($type == 3){
            $len = mb_strlen($str,'utf-8');
            if($len > 50){
                return false;
            }
            return true;
        }elseif($type == 4){
            $len = mb_strlen($str,'utf-8');
            if($len > 200){
                return false;
            }
            return true;
        }elseif($type == 5){
            if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$str)){
                return true;
            }else{
                return false;
            }
        }elseif($type == 6){
            if(preg_match('/^1[34578]\d{9}$/',$str)){
                return true;
            }else{
                return false;
            }
        }elseif($type == 7){
            if(preg_match('/^[?=.*0-9a-zA-Z]+$/u',$str)){
                if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{3,}$/',$str)){
                    return true;
                }
                return false;
            }else{
                return false;
            }
        }
    }

    /**
     * 获取加密规则密码
     * @param string $password
     * @param string $salt
     * @return array
     */
    public static function getPassword($password='123456', $salt='')
    {
        $string = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        if($password == '123456'){
            $salt = '';
            $toPassword = md5($password);
        }else{
            if(empty($salt)){
                $salt = substr(str_shuffle($string) ,mt_rand(0, strlen($string)-11), 8);
            }
            $toPassword = md5($password.$salt);
        }

        return ['password'=>$toPassword, 'salt'=>$salt];
    }

    /**
     * 获取当前微秒时间戳
     * @return float
     */
    public static function microTimes()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }
}


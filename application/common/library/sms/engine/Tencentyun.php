<?php

namespace app\common\library\sms\engine;

use app\common\library\sms\engine\buffer\TencentUnit;

class Tencentyun
{
    private $url;
//    private $appid;
//    private $appkey;
    private $config;
    private $util;

    /**
     * 构造函数
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms";
//        $this->appid = '1400377283';
//        $this->appkey = '1e4578077708c4c89167fd10fa2fb4d9';
        $this->util = new TencentUnit();
    }

    /**
     * 获取模板id
     * @param string $type
     * @return mixed|null
     */
    private function getTempId($type='login')
    {
        $tempList = [
            'login' =>  '619235'
        ];
        if(!in_array($type, array_keys($tempList))) return null;

        return $tempList[$type];
    }

    /**
     * 普通单发
     *
     * 普通单发需明确指定内容，如果有多个签名，请在内容中以[]的方式添加到信息内容中，否则系统将使用默认签名。
     *
     * @param int    $type        短信类型，0 为普通短信，1 营销短信
     * @param string $nationCode  国家码，如 86 为中国
     * @param string $phoneNumber 不带国家码的手机号
     * @param string $msg         信息内容，必须与申请的模板格式一致，否则将返回错误
     * @param string $extend      扩展码，可填空串
     * @param string $ext         服务端原样返回的参数，可填空串
     * @return string 应答json字符串，详细内容参见腾讯云协议文档
     */
    public function send($type, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "")
    {
        $random = $this->util->getRandom();
        $curTime = time();
        $wholeUrl = $this->url . "?sdkappid=" . $this->config['appid'] . "&random=" . $random;

        // 按照协议组织 post 包体
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "".$nationCode;
        $tel->mobile = "".$phoneNumber;

        $data->tel = $tel;
        $data->type = (int)$type;
        $data->msg = $msg;
        $data->sig = hash("sha256",
            "appkey=".$this->config['appkey']."&random=".$random."&time="
            .$curTime."&mobile=".$phoneNumber, FALSE);
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;

        return $this->util->sendCurlPost($wholeUrl, $data);
    }

    /**
     * 指定模板单发
     *
     * @param string $nationCode  国家码，如 86 为中国
     * @param string $phoneNumber 不带国家码的手机号
     * @param int    $type     模板 id
     * @param array  $params      模板参数列表，如模板 {1}...{2}...{3}，那么需要带三个参数
     * @param string $extend      扩展码，可填空串
     * @param string $ext         服务端原样返回的参数，可填空串
     * @return string 应答json字符串，详细内容参见腾讯云协议文档
     */
    public function sendSms($nationCode, $phoneNumber, $type, $params, $extend = "", $ext = "")
    {
        $random = $this->util->getRandom();
        $curTime = time();
        $wholeUrl = $this->url . "?sdkappid=" . $this->config['appid'] . "&random=" . $random;

        // 按照协议组织 post 包体
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "".$nationCode;
        $tel->mobile = "".$phoneNumber;

        $data->tel = $tel;
        $data->sig = $this->util->calculateSigForTempl($this->config['appkey'], $random, $curTime, $phoneNumber);
        $data->tpl_id = $this->getTempId($type);
        $data->params = $params;
        $data->sign = $this->config['sign'];
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;

        $result = $this->util->sendCurlPost($wholeUrl, $data);
        return json_decode($result, true);
    }
}
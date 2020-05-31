<?php
/**
 * Created by PhpStorm.
 * User: Eddy
 * Date: 2020/5/30
 * Time: 19:47
 */

namespace app\api\controller\action;


use app\common\model\Illegal;
use app\common\traits\ApiHelper;
use think\Request;

class Egal
{
    public $User,$request;
    public function __construct($base = '')
    {
        if(empty($base)) return ApiHelper::output(20000);
        $this->User = $base;
        $this->request = Request::instance();
    }

    /**
     * 违章列表
     * @return array
     */
    public function lists()
    {
        $User = $this->User;
        $param = $this->request->param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $page_size = empty($param['page_size']) ? config('paginate.list_rows') : $param['page_size'];
        $map = ['user_id'=>$User['user_id'], 'status'=>0];
        $res = Illegal::where($map)->field('user_id,update_time', true)->paginate(['paginate'=>$page_size, 'page'=>$page]);
        $info = $res->toArray();
        $total = Illegal::calcIllegalByMap($map);
        $total['state'] = $total['number'] > 0 ? true : false;
        $pagination = ApiHelper::pagination($info, $page);
        return ApiHelper::output(10000, $info['data'], compact('pagination', 'total','User'));
    }
}